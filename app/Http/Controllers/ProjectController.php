<?php

// app/Http/Controllers/ProjectController.php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            $projects = Project::with(['manager', 'tasks'])->get();
        } elseif ($user->hasRole('project_manager')) {
            $projects = Project::where('manager_id', $user->id)
                ->orWhereHas('members', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['manager', 'tasks'])
                ->get();
        } else {
            $projects = $user->projects()->with(['manager', 'tasks'])->get();
        }

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager_id' => Auth::id(),
        ]);

        if ($request->has('members')) {
            $project->members()->attach($request->members);
        }

        return response()->json($project->load('manager', 'members', 'tasks'), 201);
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        
        return response()->json($project->load('manager', 'members', 'tasks'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project->update($request->only(['name', 'description', 'start_date', 'end_date']));

        if ($request->has('members')) {
            $project->members()->sync($request->members);
        }

        return response()->json($project->load('manager', 'members', 'tasks'));
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(null, 204);
    }
}