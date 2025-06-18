<?php

declare(strict_types=1);

namespace App\Http\Requests\Courses;

use Illuminate\Foundation\Http\FormRequest;

final class EnrollStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'exists:users,id'],
        ];
    }
}
