<?php

namespace App\Enums;

enum ViolationStatus: string
{
    case Pending = 'pending';
    case Resolved = 'resolved';
}
