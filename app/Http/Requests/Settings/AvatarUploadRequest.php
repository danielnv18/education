<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

final class AvatarUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:10240'], // 10MB limit (10240 KB)
        ];
    }
}
