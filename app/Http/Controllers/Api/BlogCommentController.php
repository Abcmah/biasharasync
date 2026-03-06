<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function store(Request $request, $slug): JsonResponse
    {
        $blog = Blog::where('slug', $slug)->where('is_published', true)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer|exists:blog_comments,id',
        ]);

        // If replying, ensure parent belongs to the same blog
        if (!empty($validated['parent_id'])) {
            $parent = Comment::where('id', $validated['parent_id'])
                ->where('blog_id', $blog->id)
                ->whereNull('parent_id')
                ->firstOrFail();
        }

        $comment = new Comment();
        $comment->blog_id = $blog->id;
        $comment->parent_id = $validated['parent_id'] ?? null;
        $comment->name = $validated['name'];
        $comment->email = $validated['email'];
        $comment->comment = $validated['comment'];
        $comment->status = 'pending';
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'Your comment has been submitted and is awaiting moderation.',
        ]);
    }
}
