<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RenewContractRequest extends FormRequest
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
            'new_end_date' => ['required', 'date', 'after:today'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
