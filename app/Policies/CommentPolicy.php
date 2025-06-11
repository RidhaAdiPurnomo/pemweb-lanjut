<?php

// app/Policies/CommentPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function delete(User $user, Comment $comment)
    {
        return $user->hasRole('admin') || $comment->user_id === $user->id
            ? Response::allow()
            : Response::deny('You do not have permission to delete this comment.');
    }
}