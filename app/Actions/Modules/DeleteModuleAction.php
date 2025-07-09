<?php

declare(strict_types=1);

namespace App\Actions\Modules;

use App\Models\Module;
use Illuminate\Support\Facades\DB;

final readonly class DeleteModuleAction
{
    /**
     * Execute the action.
     */
    public function handle(Module $module): void
    {
        DB::transaction(function () use ($module): void {
            // Delete all the lessons associated with the module
            $module->lessons()->delete();

            // Delete the module itself
            $module->delete();
        });
    }
}
