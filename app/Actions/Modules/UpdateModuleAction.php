<?php

declare(strict_types=1);

namespace App\Actions\Modules;

use App\Models\Module;
use Illuminate\Support\Facades\DB;

final readonly class UpdateModuleAction
{
    /**
     * Execute the action.
     *
     * @param  array<string, string|bool|int>  $data
     */
    public function handle(Module $module, array $data): Module
    {
        return DB::transaction(function () use ($module, $data): Module {
            $module->update($data);

            return $module;
        });
    }
}
