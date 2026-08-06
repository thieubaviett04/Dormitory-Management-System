<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'student_code' => trim((string) $this->input('student_code')),
            'full_name' => trim((string) $this->input('full_name')),
            'email' => strtolower(trim((string) $this->input('email'))),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $student = Student::query()
            ->where('student_code', $this->input('student_code'))
            ->first();

        return [
            'student_code' => ['required', 'string', 'max:20'],
            'full_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student?->id),
            ],
            'phone_number' => ['nullable', 'string', 'max:15'],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'room_id' => [
                'required',
                'integer',
                Rule::exists('rooms', 'id')->where(
                    fn (Builder $query) => $query->where('status', '!=', 'maintenance')
                ),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'Ngày sinh không được nằm trong tương lai.',
            'email.unique' => 'Email này đã thuộc về một sinh viên khác.',
            'room_id.exists' => 'Phòng không tồn tại hoặc đang được bảo trì.',
        ];
    }
}
