<?php

// app/Policies/ProjectPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    public function view(User $user, Project $project)
    {
        return $user->hasRole('admin') || 
               $project->manager_id === $user->id || 
               $project->members->contains($user->id)
            ? Response::allow()
            : Response::deny('You do not have permission to view this project.');
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['admin', 'project_manager'])
            ? Response::allow()
            : Response::deny('You do not have permission to create projects.');
    }

    public function update(User $user, Project $project)
    {
        return $user->hasRole('admin') || $project->manager_id === $user->id
            ? Response::allow()
            : Response::deny('You do not have permission to update this project.');
    }

    public function delete(User $user, Project $project)
    {
        return $user->hasRole('admin')
            ? Response::allow()
            : Response::deny('You do not have permission to delete this project.');
    }

    public function createTasks(User $user, Project $project)
    {
        return $user->hasRole('admin') || 
               ($user->hasRole('project_manager') && $project->manager_id === $user->id)
            ? Response::allow()
            : Response::deny('You do not have permission to create tasks in this project.');
    }
}