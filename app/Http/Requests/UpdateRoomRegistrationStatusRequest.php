<?php

namespace App\Http\Requests;

use App\Enums\RoomRegistrationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRegistrationStatusRequest extends FormRequest
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
            'status' => ['required', Rule::in(RoomRegistrationStatus::reviewValues())],
            'rejected_reason' => [
                Rule::requiredIf($this->input('status') === RoomRegistrationStatus::Rejected->value),
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rejected_reason.required' => 'Vui lòng nhập lý do từ chối.',
        ];
    }
}
