<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\StoreMediaUpload;
use App\Data\MediaData;
use App\Http\Requests\StoreMediaRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

final readonly class MediaUploadController
{
    public function __invoke(StoreMediaRequest $request, #[CurrentUser] User $user, StoreMediaUpload $action): JsonResponse
    {
        $media = $action->handle($user, $request->file('file'), $request->collection());

        return response()->json(MediaData::fromMedia($media), 201);
    }
}
