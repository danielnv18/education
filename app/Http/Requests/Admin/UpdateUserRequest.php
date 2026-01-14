<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->route('user')->id],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'avatar' => ['nullable', 'string', 'url'],
        ];
    }
}
