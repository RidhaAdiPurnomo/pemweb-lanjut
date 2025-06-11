<?php

// app/Http/Controllers/CommentController.php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Task $task)
    {
        $this->authorize('view', $task->project);

        $comments = $task->comments()->with('user')->get();

        return response()->json($comments);
    }

    public function store(Request $request, Task $task)
    {
        $this->authorize('view', $task->project);

        $request->validate([
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments');
        }

        $comment = Comment::create([
            'content' => $request->content,
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'attachment_path' => $attachmentPath,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        if ($comment->attachment_path) {
            Storage::delete($comment->attachment_path);
        }

        $comment->delete();

        return response()->json(null, 204);
    }
}