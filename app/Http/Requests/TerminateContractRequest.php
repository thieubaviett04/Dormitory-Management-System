<?php

namespace App\Http\Requests;

use App\Enums\AllocationReleaseReason;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TerminateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'terminated_at' => ['nullable', 'date', 'before_or_equal:now'],
            'reason' => ['required', 'string', 'max:1000'],
            'release_reason' => [
                'nullable',
                Rule::in([
                    AllocationReleaseReason::CheckedOut->value,
                    AllocationReleaseReason::ContractTerminated->value,
                ]),
            ],
        ];
    }
}
