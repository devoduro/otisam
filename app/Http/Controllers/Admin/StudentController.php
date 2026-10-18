<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,suspended,deactivated'],
            'class_level' => ['nullable', 'string', 'max:30'],
            'verified' => ['nullable', 'in:yes,no'],
        ]);

        $students = User::query()
            ->where('account_type', AccountType::Student->value)
            ->with('studentProfile')
            ->when($filters['q'] ?? null, function ($query, $q) {
                $phone = Phone::clean($q);
                $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")
                    ->when($phone && strlen($phone) >= 4, fn ($p) => $p->orWhere('phone', 'like', "%{$phone}%")));
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['class_level'] ?? null, fn ($query, $level) => $query->whereHas('studentProfile', fn ($p) => $p->where('class_level', $level)))
            ->when(($filters['verified'] ?? null) === 'yes', fn ($query) => $query->whereNotNull('phone_verified_at'))
            ->when(($filters['verified'] ?? null) === 'no', fn ($query) => $query->whereNull('phone_verified_at'))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.students.index', compact('students', 'filters'));
    }

    public function show(User $student): View
    {
        abort_unless($student->isStudent(), 404);

        $student->load('studentProfile');

        return view('admin.students.show', compact('student'));
    }
}
