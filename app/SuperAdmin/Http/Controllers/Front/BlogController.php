<?php

namespace App\SuperAdmin\Http\Controllers\Front;

use App\Models\Blog;

class BlogController extends FrontBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->seoDetail = (object) [
            'seo_title' => 'Blog',
            'seo_description' => 'Latest news and updates',
            'seo_keywords' => 'blog, news, updates',
        ];

        $this->showFullHeader = false;
        $this->breadcrumbTitle = 'Blog';

        $this->featuredBlog = Blog::published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->first();

        $blogsQuery = Blog::published()->orderBy('published_at', 'desc');

        if ($this->featuredBlog) {
            $blogsQuery->where('id', '!=', $this->featuredBlog->id);
        }

        $this->blogs = $blogsQuery->paginate(9);

        return view('front.blog.index', $this->data);
    }

    public function show($slug)
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();

        $this->seoDetail = (object) [
            'seo_title' => $blog->seo_title ?: $blog->title,
            'seo_description' => $blog->seo_description ?: $blog->excerpt,
            'seo_keywords' => $blog->seo_keywords ?: '',
        ];

        $this->showFullHeader = false;
        $this->breadcrumbTitle = $blog->title;

        $this->blog = $blog;
        $this->comments = $blog->approvedComments()
            ->whereNull('parent_id')
            ->with(['approvedReplies' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();
        $this->commentsCount = $blog->approvedComments()->count();

        $this->relatedBlogs = Blog::published()
            ->where('id', '!=', $blog->id)
            ->when($blog->category, function ($q) use ($blog) {
                $q->where('category', $blog->category);
            })
            ->orderBy('published_at', 'desc')
            ->limit(2)
            ->get();

        return view('front.blog.show', $this->data);
    }
}
