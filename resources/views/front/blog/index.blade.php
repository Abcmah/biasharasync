@extends('front.front_layouts')

@section('styles')
<style>
    /* Blog Section */
    .news-section {
        padding: 80px 0 100px;
        background: #ffffff;
        margin-top: -60px;
    }

    /* Section Header */
    .blog-section-header {
        text-align: center;
        margin-bottom: 56px;
    }
    .blog-section-header h2 {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #2563eb;
        margin-bottom: 12px;
    }
    .blog-section-header h1 {
        font-size: 40px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    /* Featured Card */
    .featured-news-card {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        background: white;
        border-radius: 24px;
        overflow: hidden;
        margin-bottom: 64px;
        border: 1px solid #e2e8f0;
        transition: box-shadow 0.3s ease;
    }
    .featured-news-card:hover {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
    }
    .featured-image {
        position: relative;
        overflow: hidden;
    }
    .featured-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }
    .featured-news-card:hover .featured-image img {
        transform: scale(1.03);
    }
    .featured-content {
        padding: 48px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .featured-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #2563eb;
        margin-bottom: 20px;
    }
    .featured-label i { font-size: 14px; }
    .featured-title {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 16px;
        line-height: 1.25;
        letter-spacing: -0.3px;
    }
    .featured-excerpt {
        color: #64748b;
        font-size: 16px;
        margin-bottom: 24px;
        line-height: 1.7;
    }
    .featured-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 28px;
    }
    .featured-author-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #0f172a;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        flex-shrink: 0;
    }
    .featured-meta-text .author-name {
        display: block;
        font-weight: 700;
        color: #0f172a;
        font-size: 14px;
    }
    .featured-meta-text .post-date {
        font-size: 13px;
        color: #94a3b8;
    }
    .read-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #0f172a;
        color: white;
        padding: 13px 28px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        align-self: flex-start;
        transition: all 0.25s ease;
        letter-spacing: 0.2px;
    }
    .read-more-btn:hover {
        background: #2563eb;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
    }
    .read-more-btn i {
        font-size: 16px;
        transition: transform 0.2s;
    }
    .read-more-btn:hover i {
        transform: translateX(3px);
    }

    /* Grid Header */
    .grid-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
    }
    .grid-header h3 {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .grid-header .article-count {
        font-size: 14px;
        color: #94a3b8;
        font-weight: 500;
    }

    /* News Grid */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
    }
    .news-item {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .news-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
        border-color: transparent;
    }
    .news-item-image {
        position: relative;
        overflow: hidden;
    }
    .news-item-image img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }
    .news-item:hover .news-item-image img {
        transform: scale(1.05);
    }
    .news-item-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .news-category {
        display: inline-block;
        padding: 5px 14px;
        background: #f1f5f9;
        color: #2563eb;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 14px;
        align-self: flex-start;
    }
    .news-item-title {
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 10px;
        line-height: 1.35;
        letter-spacing: -0.2px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-item-excerpt {
        color: #64748b;
        font-size: 15px;
        margin-bottom: 20px;
        line-height: 1.65;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-item-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }
    .post-date {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .post-date i { font-size: 14px; }
    .text-link {
        color: #2563eb;
        font-weight: 700;
        text-decoration: none;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: gap 0.2s ease;
    }
    .text-link:hover {
        gap: 8px;
    }

    /* Pagination */
    .blog-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin-top: 64px;
    }
    .blog-pagination .page-link {
        padding: 10px 16px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        color: #475569;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s ease;
        background: white;
    }
    .blog-pagination .page-link:hover {
        background: #0f172a;
        color: white;
        border-color: #0f172a;
    }
    .blog-pagination .page-link.active {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }
    .blog-pagination .disabled .page-link {
        opacity: 0.4;
        pointer-events: none;
    }

    /* Empty State */
    .no-blogs-message {
        text-align: center;
        padding: 80px 20px;
    }
    .empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
    }
    .empty-icon i {
        font-size: 32px;
        color: #94a3b8;
    }
    .no-blogs-message h4 {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .no-blogs-message p {
        color: #64748b;
        font-size: 16px;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .featured-news-card { grid-template-columns: 1fr; }
        .featured-content { padding: 32px; }
        .news-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
        .news-grid { grid-template-columns: 1fr; }
        .blog-section-header h1 { font-size: 30px; }
        .featured-title { font-size: 24px; }
        .featured-content { padding: 24px; }
        .grid-header { flex-direction: column; align-items: flex-start; gap: 4px; }
    }
</style>
@endsection

@section('content')
<section class="news-section">
    <div class="container">

        <!-- Section Header -->
        <div class="blog-section-header">
            <h2>Our Blog</h2>
            <h1>Insights & Updates</h1>
        </div>

        @if($featuredBlog)
        <div class="featured-news-card">
            <div class="featured-image">
                @if($featuredBlog->featured_image_url)
                    <img src="{{ $featuredBlog->featured_image_url }}" alt="{{ $featuredBlog->title }}">
                @else
                    <img src="https://via.placeholder.com/800x500?text=Blog" alt="{{ $featuredBlog->title }}">
                @endif
            </div>
            <div class="featured-content">
                <span class="featured-label">
                    <i class="bi bi-star-fill"></i>
                    Featured Article
                </span>
                @if($featuredBlog->category)
                    <span class="news-category">{{ $featuredBlog->category }}</span>
                @endif
                <h2 class="featured-title">{{ $featuredBlog->title }}</h2>
                <p class="featured-excerpt">{{ $featuredBlog->excerpt ?: Str::limit(strip_tags($featuredBlog->content), 200) }}</p>
                <div class="featured-meta">
                    <div class="featured-author-avatar">
                        {{ strtoupper(substr($featuredBlog->author_name ?: 'A', 0, 1)) }}
                    </div>
                    <div class="featured-meta-text">
                        <span class="author-name">{{ $featuredBlog->author_name ?: 'Admin' }}</span>
                        <span class="post-date">{{ $featuredBlog->published_at ? $featuredBlog->published_at->format('M d, Y') : $featuredBlog->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <a href="{{ route('front.blog.show', $featuredBlog->slug) }}" class="read-more-btn">
                    Read Full Article <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        @endif

        @if($blogs->count() > 0)

        <div class="grid-header">
            <h3>Latest Articles</h3>
            <span class="article-count">{{ $blogs->total() }} {{ Str::plural('article', $blogs->total()) }}</span>
        </div>

        <div class="news-grid">
            @foreach($blogs as $blog)
            <article class="news-item">
                <div class="news-item-image">
                    @if($blog->featured_image_url)
                        <img src="{{ $blog->featured_image_url }}" alt="{{ $blog->title }}">
                    @else
                        <img src="https://via.placeholder.com/400x250?text=Blog" alt="{{ $blog->title }}">
                    @endif
                </div>
                <div class="news-item-body">
                    @if($blog->category)
                        <span class="news-category">{{ $blog->category }}</span>
                    @endif
                    <h3 class="news-item-title">{{ $blog->title }}</h3>
                    <p class="news-item-excerpt">{{ $blog->excerpt ?: Str::limit(strip_tags($blog->content), 120) }}</p>
                    <div class="news-item-footer">
                        <span class="post-date">
                            <i class="bi bi-calendar3"></i>
                            {{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}
                        </span>
                        <a href="{{ route('front.blog.show', $blog->slug) }}" class="text-link">Read More <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        @if($blogs->hasPages())
        <div class="blog-pagination">
            @if($blogs->onFirstPage())
                <span class="disabled"><span class="page-link">&larr; Previous</span></span>
            @else
                <a href="{{ $blogs->previousPageUrl() }}" class="page-link">&larr; Previous</a>
            @endif

            @foreach($blogs->getUrlRange(1, $blogs->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="page-link {{ $page == $blogs->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach

            @if($blogs->hasMorePages())
                <a href="{{ $blogs->nextPageUrl() }}" class="page-link">Next &rarr;</a>
            @else
                <span class="disabled"><span class="page-link">Next &rarr;</span></span>
            @endif
        </div>
        @endif

        @else
        <div class="no-blogs-message">
            <div class="empty-icon">
                <i class="bi bi-journal-text"></i>
            </div>
            <h4>No articles yet</h4>
            <p>Check back soon for new content.</p>
        </div>
        @endif

    </div>
</section>
@endsection
