<?php

namespace App\Enums;

enum AllocationReleaseReason: string
{
    case Transferred = 'transferred';
    case CheckedOut = 'checked_out';
    case ContractExpired = 'contract_expired';
    case ContractTerminated = 'contract_terminated';
}
