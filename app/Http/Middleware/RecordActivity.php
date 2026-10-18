<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Writes an audit entry for every change a signed-in staff member successfully makes (any POST /
 * PUT / PATCH / DELETE under /admin), so the audit log shows who did what and when. Ported from
 * jhs_portal. Request input is never stored. Failed or bounced submissions are not logged, and a
 * logging failure never breaks the action that was just performed.
 */
class RecordActivity
{
    /** Route-name prefix => [category, noun]. The first match wins, so specific prefixes go first. */
    private const AREAS = [
        'admin.teachers.capabilities' => ['user', 'teacher capabilities'],
        'admin.teachers' => ['user', 'teacher'],
        'admin.students' => ['user', 'student'],
        'admin.staff' => ['user', 'staff account'],
        'admin.roles' => ['user', 'role'],
        'admin.settings' => ['settings', 'setting'],
    ];

    private const VERBS = [
        'store' => 'Added', 'update' => 'Updated', 'destroy' => 'Deleted', 'approve' => 'Approved',
        'reject' => 'Rejected', 'request-info' => 'Asked for more information on', 'suspend' => 'Suspended',
        'activate' => 'Reactivated', 'reset-password' => 'Reset the password of',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $this->record($request, $response);
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }

    private function record(Request $request, Response $response): void
    {
        if ($request->isMethodSafe() || !$request->user() || $response->getStatusCode() >= 400 || $request->attributes->get('audit.recorded')) {
            return;
        }

        $name = (string) $request->route()?->getName();
        if (!Str::startsWith($name, 'admin.')) {
            return;
        }

        // A form that bounced back with validation errors or an error message changed nothing.
        if ($request->hasSession() && ($request->session()->get('errors')?->any() || $request->session()->has('error'))) {
            return;
        }

        [$category, $noun] = $this->area($name);
        $subject = $this->subject($request);
        $verb = self::VERBS[Str::afterLast($name, '.')] ?? ($request->method() === 'DELETE' ? 'Deleted' : 'Updated');

        AuditLog::create([
            'user_id' => $request->user()->id,
            'category' => $category,
            'action' => Str::afterLast($name, '.'),
            'description' => Str::limit(trim("{$verb} {$noun}" . ($subject ? ": {$subject}" : '')), 480),
            'route_name' => $name,
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
        ]);
    }

    private function area(string $name): array
    {
        foreach (self::AREAS as $prefix => $area) {
            if ($name === $prefix || Str::startsWith($name, $prefix . '.')) {
                return $area;
            }
        }

        return ['other', Str::of($name)->after('admin.')->before('.')->replace('-', ' ')->toString()];
    }

    /** What was acted on: the first model in the route, by its name. */
    private function subject(Request $request): ?string
    {
        foreach ((array) $request->route()?->parameters() as $parameter) {
            if ($parameter instanceof Model) {
                return $parameter->name ?? $parameter->title ?? null;
            }
        }

        return null;
    }
}
