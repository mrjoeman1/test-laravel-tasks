<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $projectId = $request->get('project_id');
        $tasks = Task::with('project')->orderBy('priority');
        if ($projectId) {
            $found = Project::query()->where(['id' => $projectId])->exists();
            if (!$found) {
                throw new NotFoundHttpException();
            }
            $tasks->where(['project_id' => $projectId]);
        }
        return Inertia::render('Task/List', [
            'projects' => Project::all(),
            'tasks' => $tasks->get(),
            'projectId' => $projectId,
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('Task/Edit', [
            'projects' => Project::all(),
        ]);
    }

    public function store(Request $request): TaskResource
    {
        $request->validate([
            'name' => 'required|max:200',
            'project_id' => 'required',
            'priority' => 'required|min:0',
        ]);
        $task = Task::create([
            'name' => $request->post('name'),
            'project_id' => $request->post('project_id'),
            'priority' => $request->post('priority'),
        ]);
        return new TaskResource($task);
    }

    public function destroy(Task $task): void
    {
        $task->deleteOrFail();
    }

    public function edit(Task $task): InertiaResponse
    {
        return Inertia::render('Task/Edit', [
            'task' => $task,
            'projects' => Project::all(),
        ]);
    }

    public function update(Request $request, Task $task): TaskResource
    {
        if ($request->isMethod('patch')) {
            return $this->patch($request, $task);
        }
        // Handle 'PUT' requests
        $request->validate([
            'name' => 'required|max:200',
            'project_id' => 'required',
            'priority' => 'required|min:0',
        ]);
        $task->updateOrFail([
            'name' => $request->post('name'),
            'project_id' => $request->post('project_id'),
            'priority' => $request->post('priority'),
        ]);
        return new TaskResource($task);
    }

    private function patch(Request $request, Task $task): TaskResource
    {
        $request->validate(['priority' => 'required|min:0']);
        $newPriority = $request->post('priority');
        DB::transaction(function () use ($task, $newPriority) {
            // Update only rows that actually have changed priority and do it in a single query
            if ($newPriority > $task->priority) {
                Task::query()
                    ->where('priority', '>', $task->priority)
                    ->where('priority', '<=', $newPriority)
                    ->decrement('priority');
            } else {
                Task::query()
                    ->where('priority', '>=', $newPriority)
                    ->where('priority', '<', $task->priority)
                    ->increment('priority');
            }
            $task->priority = $newPriority;
            $task->saveOrFail();
        });
        return new TaskResource($task);
    }
}
