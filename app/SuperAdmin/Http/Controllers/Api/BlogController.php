<?php

namespace App\SuperAdmin\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogs = Blog::orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $blogs->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status')) {
            if ($request->status == 'published') {
                $blogs->where('is_published', true);
            } elseif ($request->status == 'draft') {
                $blogs->where('is_published', false);
            }
        }

        $blogs = $blogs->paginate($request->get('limit', 15));

        return response()->json(['message' => 'Success', 'blogs' => $blogs]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'excerpt' => 'nullable|string|max:500',
            'author_name' => 'nullable|string|max:100',
            'is_published' => 'nullable',
            'is_featured' => 'nullable',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
        ]);

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->slug = Blog::generateUniqueSlug($request->title);
        $blog->category = $request->category;
        $blog->excerpt = $request->excerpt;
        $blog->content = $request->content;
        $blog->author_name = $request->author_name ?? 'Admin';
        $blog->is_published = filter_var($request->is_published, FILTER_VALIDATE_BOOLEAN);
        $blog->is_featured = filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN);
        $blog->seo_title = $request->seo_title;
        $blog->seo_description = $request->seo_description;
        $blog->seo_keywords = $request->seo_keywords;

        if ($blog->is_published) {
            $blog->published_at = Carbon::now();
        }

        if ($request->hasFile('featured_image')) {
            $blog->featured_image = $this->uploadImage($request->file('featured_image'));
        }

        $blog->save();

        return response()->json(['message' => 'Blog created successfully', 'blog' => $blog]);
    }

    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->load('comments');

        return response()->json(['message' => 'Success', 'blog' => $blog]);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'excerpt' => 'nullable|string|max:500',
            'author_name' => 'nullable|string|max:100',
            'is_published' => 'nullable',
            'is_featured' => 'nullable',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
        ]);

        $blog->title = $request->title;
        $blog->slug = Blog::generateUniqueSlug($request->title, $blog->id);
        $blog->category = $request->category;
        $blog->excerpt = $request->excerpt;
        $blog->content = $request->content;
        $blog->author_name = $request->author_name ?? $blog->author_name;
        $blog->is_featured = filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN);
        $blog->seo_title = $request->seo_title;
        $blog->seo_description = $request->seo_description;
        $blog->seo_keywords = $request->seo_keywords;

        $wasPublished = $blog->is_published;
        $blog->is_published = filter_var($request->is_published, FILTER_VALIDATE_BOOLEAN);

        if (!$wasPublished && $blog->is_published) {
            $blog->published_at = Carbon::now();
        }

        if ($request->hasFile('featured_image')) {
            $this->deleteImage($blog->featured_image);
            $blog->featured_image = $this->uploadImage($request->file('featured_image'));
        }

        $blog->save();

        return response()->json(['message' => 'Blog updated successfully', 'blog' => $blog]);
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $this->deleteImage($blog->featured_image);
        $blog->delete();

        return response()->json(['message' => 'Blog deleted successfully']);
    }

    public function togglePublish($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->is_published = !$blog->is_published;

        if ($blog->is_published && !$blog->published_at) {
            $blog->published_at = Carbon::now();
        }

        $blog->save();

        $status = $blog->is_published ? 'published' : 'unpublished';

        return response()->json(['message' => "Blog {$status} successfully", 'blog' => $blog]);
    }

    public function comments(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $comments = $blog->comments()->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status != 'all') {
            $comments->where('status', $request->status);
        }

        $comments = $comments->paginate($request->get('limit', 15));

        return response()->json(['message' => 'Success', 'comments' => $comments]);
    }

    public function updateCommentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $comment = Comment::findOrFail($id);
        $comment->status = $request->status;
        $comment->save();

        return response()->json(['message' => 'Comment status updated', 'comment' => $comment]);
    }

    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    protected function uploadImage($file)
    {
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $uploadPath = public_path('uploads/blogs');

        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $filename);

        return $filename;
    }

    protected function deleteImage($filename)
    {
        if ($filename) {
            $path = public_path('uploads/blogs/' . $filename);
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }
}
