<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Explicit audit entries for domain events (approvals, role changes, suspensions...). Routine
 * form saves are recorded automatically by the RecordActivity middleware.
 */
class Audit
{
    /** Keys that must never reach the audit log, at any depth. */
    private const REDACT = ['password', 'password_confirmation', 'remember_token', 'code', 'otp', 'token', 'secret', 'api_key'];

    public static function record(string $category, string $action, string $description, ?Model $subject = null, ?array $old = null, ?array $new = null): AuditLog
    {
        $request = request();
        // Tells RecordActivity this request already has a (more detailed) entry.
        $request?->attributes->set("audit.recorded", true);

        return AuditLog::create([
            'user_id' => auth()->id(),
            'category' => $category,
            'action' => $action,
            'description' => Str::limit($description, 490),
            'auditable_type' => $subject?->getMorphClass(),
            'auditable_id' => $subject?->getKey(),
            'old_values' => $old !== null ? self::clean($old) : null,
            'new_values' => $new !== null ? self::clean($new) : null,
            'route_name' => $request?->route()?->getName(),
            'method' => $request?->method(),
            'ip_address' => $request?->ip(),
            'user_agent' => Str::limit((string) $request?->userAgent(), 250, ''),
        ]);
    }

    private static function clean(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_string($key) && in_array(Str::lower($key), self::REDACT, true)) {
                $values[$key] = '[redacted]';
            } elseif (is_array($value)) {
                $values[$key] = self::clean($value);
            } elseif ($value instanceof \BackedEnum) {
                $values[$key] = $value->value;
            }
        }

        return $values;
    }
}
