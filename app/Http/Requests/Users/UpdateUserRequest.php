<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'string', 'in:'.implode(',', array_column(UserRole::cases(), 'value'))],
            'email_verified' => ['nullable', 'boolean'],
        ];
    }
}
