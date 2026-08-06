<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferContractRequest extends FormRequest
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
            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'transferred_at' => ['nullable', 'date', 'before_or_equal:now'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
