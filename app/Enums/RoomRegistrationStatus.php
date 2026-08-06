<?php

namespace App\Enums;

enum RoomRegistrationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Waitlist = 'waitlist';
    case Cancelled = 'cancelled';

    /**
     * @return list<string>
     */
    public static function activeValues(): array
    {
        return [
            self::Pending->value,
            self::Waitlist->value,
            self::Approved->value,
        ];
    }

    /**
     * @return list<string>
     */
    public static function reviewValues(): array
    {
        return [
            self::Approved->value,
            self::Rejected->value,
            self::Waitlist->value,
        ];
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [
                self::Approved,
                self::Rejected,
                self::Waitlist,
                self::Cancelled,
            ], true),
            self::Waitlist => in_array($target, [
                self::Approved,
                self::Rejected,
                self::Cancelled,
            ], true),
            self::Approved, self::Rejected, self::Cancelled => false,
        };
    }
}
