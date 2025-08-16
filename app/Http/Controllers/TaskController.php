<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->integer('project_id');
        $projects = Project::orderBy('name')->get();

        $tasks = Task::when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->orderBy('priority')
            ->get();

        return view('tasks.index', compact('projects', 'tasks', 'projectId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);

        $projectId = $data['project_id'] ?? null;

        $max = Task::where('project_id', $projectId)->max('priority');
        $data['priority'] = ($max ?? 0) + 1;

        Task::create($data);

        return back()->with('status', 'Task created.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);

        DB::transaction(function () use ($task, $validated) {
            $movingProject = array_key_exists('project_id', $validated)
                && $validated['project_id'] !== $task->project_id;

            if ($movingProject) {
                $max = Task::where('project_id', $validated['project_id'])->max('priority');
                $validated['priority'] = ($max ?? 0) + 1;
            }

            $task->update($validated);
        });

        return back()->with('status', 'Task Updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        DB::transaction(function () use ($task) {
            $projectId = $task->project_id;
            $task->delete();

            // Reorder priorities 
            $tasks = Task::where('project_id', $projectId)
                ->orderBy('priority')
                ->lockForUpdate()
                ->get();

            foreach ($tasks as $i => $t) {
                $t->update(['priority' => $i + 1]);
            }
        });

        return back()->with('status', 'Task deleted.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:tasks,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $projectId = $validated['project_id'] ?? null;

            // Only order the visibly tasks
            foreach ($validated['order'] as $i => $id) {
                Task::where('id', $id)
                    ->when(!is_null($projectId), fn($q) => $q->where('project_id', $projectId))
                    ->update(['priority' => $i + 1]);
            }
        });

        return response()->json(['status' => 'ok']);
    }
}
