<?php

// app/Policies/TaskPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function update(User $user, Task $task)
    {
        return $user->hasRole('admin') || 
               $task->project->manager_id === $user->id || 
               $task->assigned_to === $user->id
            ? Response::allow()
            : Response::deny('You do not have permission to update this task.');
    }

    public function delete(User $user, Task $task)
    {
        return $user->hasRole('admin') || $task->project->manager_id === $user->id
            ? Response::allow()
            : Response::deny('You do not have permission to delete this task.');
    }
}