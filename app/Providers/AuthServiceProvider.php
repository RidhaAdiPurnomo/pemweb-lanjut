<?php
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use App\Policies\CommentPolicy;

protected $policies = [
    Project::class => ProjectPolicy::class,
    Task::class => TaskPolicy::class,
    Comment::class => CommentPolicy::class,
];