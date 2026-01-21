<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final readonly class StoreMediaUpload
{
    public function handle(User $user, UploadedFile $file, string $collection): Media
    {
        return DB::transaction(fn (): Media => $user->addMedia($file)->toMediaCollection($collection));
    }
}
