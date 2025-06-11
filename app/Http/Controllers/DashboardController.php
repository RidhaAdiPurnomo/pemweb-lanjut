<?php

// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $user = Auth::user();

        $projectsCount = $user->projects()->count();
        $tasksCount = $user->tasks()->count();
        $completedTasksCount = $user->tasks()->where('status', 'done')->count();

        $recentProjects = $user->projects()
            ->withCount('tasks')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentTasks = $user->tasks()
            ->with('project')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'projects_count' => $projectsCount,
                'tasks_count' => $tasksCount,
                'completed_tasks_count' => $completedTasksCount,
            ],
            'recent_projects' => $recentProjects,
            'recent_tasks' => $recentTasks,
        ]);
    }
}