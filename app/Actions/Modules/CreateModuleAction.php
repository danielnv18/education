<?php

declare(strict_types=1);

namespace App\Actions\Modules;

use App\Models\Module;
use Illuminate\Support\Facades\DB;

final readonly class CreateModuleAction
{
    /**
     * Handle the creation of a module.
     *
     * @param  array<string, string|bool|int>  $data
     */
    public function handle(array $data): Module
    {
        return DB::transaction(fn (): Module => Module::query()->create($data));
    }
}
