<?php

declare(strict_types=1);

namespace App\Http\Controllers\Course;

use App\Actions\Modules\CreateModuleAction;
use App\Actions\Modules\DeleteModuleAction;
use App\Actions\Modules\UpdateModuleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\CreateModuleRequest;
use App\Http\Requests\Modules\UpdateModuleRequest;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class ModuleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateModuleRequest $request, Course $course, CreateModuleAction $action): RedirectResponse
    {
        Gate::authorize('manageContent', $course);

        $data = $request->validated();
        $action->handle($data);

        return to_route('courses.content.index', $course)->with('success', 'Module created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateModuleRequest $request, Course $course, Module $module, UpdateModuleAction $action): RedirectResponse
    {
        Gate::authorize('manageContent', $course);

        $data = $request->validated();
        $action->handle($module, $data);

        return to_route('courses.content.index', $course)->with('success', 'Module Update successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Module $module, DeleteModuleAction $action): RedirectResponse
    {
        Gate::authorize('manageContent', $course);

        $action->handle($module);

        return to_route('courses.content.index', $course)->with('success', 'Module delete successfully');
    }
}
