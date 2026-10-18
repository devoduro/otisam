<?php

namespace App\Enums;

/** What kind of account this is. Decides the profile and home dashboard, never privileges. */
enum AccountType: string
{
    case Student = 'student';
    case Teacher = 'teacher';
    case Staff = 'staff';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
