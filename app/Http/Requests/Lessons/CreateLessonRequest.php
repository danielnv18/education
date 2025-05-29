<?php

declare(strict_types=1);

namespace App\Http\Requests\Lessons;

use App\Enums\LessonType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class CreateLessonRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'module_id' => ['required', 'exists:modules,id'],
            'order' => ['integer', 'min:0'],
            'type' => ['required', new Enum(LessonType::class)],
            'is_published' => ['boolean'],
        ];
    }
}
