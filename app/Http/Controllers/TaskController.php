<?php

// app/Http/Controllers/TaskController.php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $tasks = $project->tasks()->with(['assignedTo', 'createdBy', 'comments.user'])->get();

        return response()->json($tasks);
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('create-tasks', $project);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'to_do',
            'due_date' => $request->due_date,
            'project_id' => $project->id,
            'assigned_to' => $request->assigned_to,
            'created_by' => Auth::id(),
        ]);

        return response()->json($task->load('assignedTo', 'createdBy'), 201);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task->project);

        return response()->json($task->load('assignedTo', 'createdBy', 'comments.user', 'project'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:to_do,in_progress,done',
            'due_date' => 'nullable|date',
            'assigned_to' => 'sometimes|exists:users,id',
        ]);

        $task->update($request->only(['title', 'description', 'status', 'due_date', 'assigned_to']));

        return response()->json($task->load('assignedTo', 'createdBy'));
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task->project);

        $task->delete();

        return response()->json(null, 204);
    }
}