<?php

namespace App\Enums;

enum TeacherVerificationStatus: string
{
    case Pending = 'pending';
    case MoreInfo = 'more_info';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending review',
            self::MoreInfo => 'More information requested',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::MoreInfo => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
