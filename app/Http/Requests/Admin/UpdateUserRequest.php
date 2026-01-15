<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(RoleEnum::Admin->value) ?? false;
    }

    /**
     * @return array<string, list<string|Unique>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            /** @phpstan-ignore property.notFound */
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->route('user')?->id],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'avatar' => ['nullable', 'string', 'url'],
        ];
    }
}
