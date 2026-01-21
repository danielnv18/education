<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        assert($user instanceof User);

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],

            'avatar_media_id' => [
                'nullable',
                'integer',
                Rule::exists('media', 'id')->where(fn (Builder $query): Builder => $query
                    ->where('model_type', User::class)
                    ->where('model_id', $user->id)
                    ->whereIn('collection_name', ['temporary', 'avatar'])),
            ],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }
}
