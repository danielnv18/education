<?php

declare(strict_types=1);

namespace App\Http\Requests\Courses;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

final class CreateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::check('create', Course::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(CourseStatus::class)],
            'is_published' => ['boolean'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'thumbnail_id' => ['nullable', 'exists:files,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
