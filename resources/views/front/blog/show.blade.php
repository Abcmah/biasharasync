@extends('front.front_layouts')

@section('styles')
<style>
    /* Reading Progress Bar */
    .reading-progress {
        position: fixed; top: 0; left: 0; width: 0%; height: 3px;
        background: #2563eb;
        z-index: 9999; transition: width 0.1s linear;
    }

    /* Hero Header */
    .article-hero {
        padding: 60px 0 40px;
        text-align: center;
        max-width: 820px;
        margin: 0 auto;
    }
    .article-hero .category-badge {
        display: inline-block;
        padding: 6px 20px;
        background: #0f172a;
        color: white;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 28px;
    }
    .article-hero h1 {
        font-size: 44px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.15;
        margin: 0 0 28px;
        letter-spacing: -0.5px;
    }
    .article-meta-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 24px;
        color: #64748b;
        font-size: 14px;
        flex-wrap: wrap;
    }
    .meta-author {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: #334155;
    }
    .meta-author-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: #0f172a;
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 14px;
    }
    .meta-dot { width: 4px; height: 4px; border-radius: 50%; background: #cbd5e1; }
    .meta-item { display: flex; align-items: center; gap: 6px; }
    .meta-item i { font-size: 15px; }

    /* Featured Image */
    .article-featured-wrapper {
        max-width: 1000px;
        margin: 0 auto 60px;
        padding: 0 20px;
    }
    .article-featured-img {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.12);
    }
    .article-featured-img img {
        width: 100%;
        display: block;
        aspect-ratio: 16 / 9;
        object-fit: cover;
    }

    /* Article Body */
    .article-body-section {
        padding-bottom: 80px;
    }
    .article-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Content Prose */
    .article-content-wrapper {
        max-width: 720px;
        margin: 0 auto;
        font-size: 18px;
        line-height: 1.85;
        color: #374151;
    }
    .article-content-wrapper > p:first-of-type::first-letter {
        font-size: 3.5em;
        font-weight: 800;
        float: left;
        line-height: 0.85;
        margin: 6px 12px 0 0;
        color: #0f172a;
    }
    .article-content-wrapper p {
        margin-bottom: 1.5em;
    }
    .article-content-wrapper h2 {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        margin: 56px 0 20px;
        letter-spacing: -0.3px;
    }
    .article-content-wrapper h3 {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        margin: 48px 0 16px;
    }
    .article-content-wrapper h4 {
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
        margin: 40px 0 14px;
    }
    .article-content-wrapper blockquote {
        position: relative;
        border: none;
        padding: 32px 40px 32px 48px;
        margin: 40px 0;
        background: #f8fafc;
        border-radius: 16px;
        font-style: italic;
        font-size: 22px;
        color: #1e293b;
        line-height: 1.6;
    }
    .article-content-wrapper blockquote::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: #2563eb;
        border-radius: 4px;
    }
    .article-content-wrapper img {
        max-width: 100%;
        border-radius: 16px;
        margin: 32px 0;
    }
    .article-content-wrapper a {
        color: #2563eb;
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-color: rgba(37, 99, 235, 0.3);
        transition: text-decoration-color 0.2s;
    }
    .article-content-wrapper a:hover {
        text-decoration-color: #2563eb;
    }
    .article-content-wrapper ul, .article-content-wrapper ol {
        padding-left: 1.5em;
        margin-bottom: 1.5em;
    }
    .article-content-wrapper li {
        margin-bottom: 0.5em;
    }
    .article-content-wrapper pre {
        background: #1e293b;
        color: #e2e8f0;
        padding: 24px;
        border-radius: 12px;
        overflow-x: auto;
        font-size: 15px;
        margin: 32px 0;
    }
    .article-content-wrapper code {
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.9em;
        color: #e11d48;
    }
    .article-content-wrapper pre code {
        background: none;
        padding: 0;
        color: inherit;
    }

    /* Tags & Share Bar */
    .article-footer-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 720px;
        margin: 50px auto 0;
        padding-top: 32px;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
        gap: 20px;
    }
    .share-article {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .share-article span {
        font-weight: 600;
        color: #64748b;
        font-size: 14px;
        margin-right: 4px;
    }
    .share-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; border-radius: 12px;
        background: #f1f5f9; color: #475569;
        text-decoration: none; font-size: 16px;
        transition: all 0.25s ease;
        border: 1px solid transparent;
    }
    .share-btn:hover {
        background: white;
        border-color: #e2e8f0;
        color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    /* Author Card */
    .author-card {
        max-width: 720px;
        margin: 60px auto 0;
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 32px;
        background: #f8fafc;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
    }
    .author-card-avatar {
        width: 72px; height: 72px; border-radius: 50%; flex-shrink: 0;
        background: #0f172a;
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 28px;
    }
    .author-card-info h4 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .author-card-info .author-label {
        font-size: 13px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    /* Comments Section */
    .comments-area {
        max-width: 720px;
        margin: 0 auto;
        padding-top: 60px;
    }
    .comments-header {
        margin-bottom: 36px;
    }
    .comments-count {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .comments-subtitle {
        color: #64748b;
        font-size: 15px;
    }

    /* Comment Form */
    .comment-form-card {
        background: white;
        padding: 32px;
        border-radius: 20px;
        margin-bottom: 48px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .reply-title {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 20px;
    }
    .form-grid-compact {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .form-group.full-width { grid-column: 1 / -1; }
    .auth-input, .modern-textarea {
        width: 100%;
        padding: 13px 18px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 15px;
        font-family: inherit;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
        background: #f8fafc;
    }
    .auth-input:focus, .modern-textarea:focus {
        border-color: #2563eb;
        background: white;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
    }
    .auth-input::placeholder, .modern-textarea::placeholder {
        color: #94a3b8;
    }
    .modern-textarea { resize: vertical; min-height: 100px; }
    .auth-submit-btn {
        grid-column: 1 / -1;
        padding: 14px 32px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        font-family: inherit;
        letter-spacing: 0.2px;
    }
    .auth-submit-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
    }
    .auth-submit-btn:active { transform: translateY(0); }
    .auth-submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }

    .comment-alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 18px;
        font-size: 14px;
        display: none;
        font-weight: 500;
    }
    .comment-alert.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .comment-alert.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    /* Comment Items */
    .comment-item {
        display: flex;
        gap: 16px;
        padding: 24px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .comment-item:last-child { border-bottom: none; }
    .comment-avatar-placeholder {
        width: 44px; height: 44px; border-radius: 14px; flex-shrink: 0;
        background: #f1f5f9;
        color: #2563eb;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 16px;
    }
    .comment-content { flex: 1; min-width: 0; }
    .comment-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .user-name { font-weight: 700; color: #0f172a; font-size: 15px; }
    .comment-date { font-size: 13px; color: #94a3b8; font-weight: 500; }
    .comment-text { font-size: 15px; line-height: 1.65; color: #475569; margin: 0; }

    /* Reply Button */
    .reply-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: none;
        border: none;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        padding: 6px 0 0;
        font-family: inherit;
        transition: color 0.2s;
    }
    .reply-btn:hover { color: #2563eb; }
    .reply-btn i { font-size: 14px; }

    /* Replies Thread */
    .comment-replies {
        margin-left: 60px;
        border-left: 2px solid #f1f5f9;
        padding-left: 20px;
    }
    .comment-replies .comment-item {
        padding: 16px 0;
    }
    .comment-replies .comment-avatar-placeholder {
        width: 36px; height: 36px; border-radius: 10px;
        font-size: 13px;
    }
    .comment-replies .user-name { font-size: 14px; }
    .comment-replies .comment-text { font-size: 14px; }

    /* Inline Reply Form */
    .inline-reply-form {
        display: none;
        margin-top: 14px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
    }
    .inline-reply-form.active { display: block; }
    .inline-reply-form .reply-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .inline-reply-form .reply-form-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }
    .inline-reply-form .close-reply-btn {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        line-height: 1;
        transition: color 0.2s;
    }
    .inline-reply-form .close-reply-btn:hover { color: #ef4444; }
    .reply-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .reply-form-grid .full-width { grid-column: 1 / -1; }
    .reply-form-grid .auth-input,
    .reply-form-grid .modern-textarea {
        padding: 10px 14px;
        font-size: 14px;
        border-radius: 10px;
    }
    .reply-form-grid .modern-textarea { min-height: 70px; }
    .reply-submit-btn {
        grid-column: 1 / -1;
        padding: 10px 20px;
        background: #0f172a;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: inherit;
    }
    .reply-submit-btn:hover { background: #2563eb; }
    .reply-submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

    .reply-alert {
        padding: 10px 14px;
        border-radius: 10px;
        margin-bottom: 12px;
        font-size: 13px;
        display: none;
        font-weight: 500;
    }
    .reply-alert.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .reply-alert.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    /* Section Divider */
    .section-divider {
        border: 0;
        border-top: 1px solid #e2e8f0;
        margin: 72px auto;
        max-width: 720px;
    }

    /* Related Articles */
    .related-section {
        max-width: 720px;
        margin: 0 auto;
        padding-top: 60px;
    }
    .related-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
    }
    .related-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .related-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    .related-card {
        text-decoration: none;
        display: block;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .related-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-color: transparent;
    }
    .related-card-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }
    .related-card-body {
        padding: 20px;
    }
    .related-card-body h5 {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.4;
        margin: 0 0 8px;
        transition: color 0.2s;
    }
    .related-card:hover h5 { color: #2563eb; }
    .related-card-body .related-date {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 500;
    }

    /* Back to Blog Link */
    .back-to-blog {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
        transition: color 0.2s;
    }
    .back-to-blog:hover { color: #2563eb; }
    .back-to-blog i { font-size: 16px; transition: transform 0.2s; }
    .back-to-blog:hover i { transform: translateX(-3px); }

    /* Responsive */
    @media (max-width: 768px) {
        .article-hero h1 { font-size: 30px; }
        .article-hero { padding: 40px 20px 30px; }
        .article-content-wrapper { font-size: 17px; }
        .article-content-wrapper > p:first-of-type::first-letter { font-size: 2.8em; }
        .article-content-wrapper blockquote { font-size: 19px; padding: 24px 24px 24px 32px; }
        .form-grid-compact { grid-template-columns: 1fr; }
        .reply-form-grid { grid-template-columns: 1fr; }
        .related-grid { grid-template-columns: 1fr; }
        .article-footer-bar { justify-content: center; }
        .author-card { flex-direction: column; text-align: center; padding: 28px 24px; }
        .comment-replies { margin-left: 32px; padding-left: 14px; }
    }
    @media (max-width: 600px) {
        .article-hero h1 { font-size: 26px; }
        .comment-meta { flex-direction: column; align-items: flex-start; gap: 4px; }
        .article-meta-row { gap: 12px; }
    }
</style>
@endsection

@section('content')

<!-- Reading Progress Bar -->
<div class="reading-progress" id="readingProgress"></div>

<article class="article-body-section">
    <div class="article-container">

        <!-- Back Link -->
        <a href="{{ route('front.blog.index') }}" class="back-to-blog">
            <i class="bi bi-arrow-left"></i> Back to Blog
        </a>

        <!-- Hero Header -->
        <header class="article-hero">
            @if($blog->category)
                <span class="category-badge">{{ $blog->category }}</span>
            @endif

            <h1>{{ $blog->title }}</h1>

            <div class="article-meta-row">
                <div class="meta-author">
                    <div class="meta-author-avatar">
                        {{ strtoupper(substr($blog->author_name ?: 'A', 0, 1)) }}
                    </div>
                    {{ $blog->author_name ?: 'Admin' }}
                </div>
                <div class="meta-dot"></div>
                <div class="meta-item">
                    <i class="bi bi-calendar3"></i>
                    {{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}
                </div>
                <div class="meta-dot"></div>
                <div class="meta-item">
                    <i class="bi bi-clock"></i>
                    {{ max(1, round(str_word_count(strip_tags($blog->content)) / 200)) }} min read
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        @if($blog->featured_image_url)
        <div class="article-featured-wrapper" style="max-width:1000px;margin:0 auto 60px;padding:0;">
            <div class="article-featured-img">
                <img src="{{ $blog->featured_image_url }}" alt="{{ $blog->title }}">
            </div>
        </div>
        @endif

        <!-- Article Content -->
        <div class="article-content-wrapper" id="articleContent">
            {!! $blog->content !!}
        </div>

        <!-- Share Bar -->
        <div class="article-footer-bar">
            <div class="share-article">
                <span>Share this article</span>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog->title) }}" target="_blank" rel="noopener noreferrer" title="Share on X" class="share-btn">
                    <i class="bi bi-twitter-x"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" title="Share on LinkedIn" class="share-btn">
                    <i class="bi bi-linkedin"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" title="Share on Facebook" class="share-btn">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="https://wa.me/?text={{ urlencode($blog->title . ' ' . request()->url()) }}" target="_blank" rel="noopener noreferrer" title="Share on WhatsApp" class="share-btn">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </div>
        </div>

        <!-- Author Card -->
        <div class="author-card">
            <div class="author-card-avatar">
                {{ strtoupper(substr($blog->author_name ?: 'A', 0, 1)) }}
            </div>
            <div class="author-card-info">
                <span class="author-label">Written by</span>
                <h4>{{ $blog->author_name ?: 'Admin' }}</h4>
            </div>
        </div>

        <hr class="section-divider">

        <!-- Comments Section -->
        <section class="comments-area">
            <div class="comments-header">
                <h3 class="comments-count">{{ $commentsCount }} {{ Str::plural('Comment', $commentsCount) }}</h3>
                <p class="comments-subtitle">Join the conversation and share your thoughts.</p>
            </div>

            <div class="comment-form-card">
                <h4 class="reply-title">Leave a Comment</h4>
                <div id="comment-alert" class="comment-alert"></div>
                <form id="blog-comment-form">
                    <div class="form-grid-compact">
                        <div class="form-group">
                            <input type="text" name="name" class="auth-input" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="auth-input" placeholder="Your Email" required>
                        </div>
                        <div class="form-group full-width">
                            <textarea name="comment" class="modern-textarea" rows="4" placeholder="Write your thoughts..." required></textarea>
                        </div>
                        <button type="submit" class="auth-submit-btn" id="comment-submit-btn">
                            Post Comment
                        </button>
                    </div>
                </form>
            </div>

            @if($comments->count() > 0)
            <div class="comments-list">
                @foreach($comments as $comment)
                <div class="comment-item">
                    <div class="comment-avatar">
                        <div class="comment-avatar-placeholder">{{ strtoupper(substr($comment->name, 0, 1)) }}</div>
                    </div>
                    <div class="comment-content">
                        <div class="comment-meta">
                            <span class="user-name">{{ $comment->name }}</span>
                            <span class="comment-date">{{ $comment->created_at->format('M d, Y') }}</span>
                        </div>
                        <p class="comment-text">{{ $comment->comment }}</p>
                        <button class="reply-btn" onclick="toggleReplyForm({{ $comment->id }})">
                            <i class="bi bi-reply"></i> Reply
                        </button>

                        <!-- Inline Reply Form -->
                        <div class="inline-reply-form" id="reply-form-{{ $comment->id }}">
                            <div class="reply-form-header">
                                <span class="reply-form-title">Replying to {{ $comment->name }}</span>
                                <button class="close-reply-btn" onclick="toggleReplyForm({{ $comment->id }})">&times;</button>
                            </div>
                            <div class="reply-alert" id="reply-alert-{{ $comment->id }}"></div>
                            <form onsubmit="submitReply(event, {{ $comment->id }})">
                                <div class="reply-form-grid">
                                    <div class="form-group">
                                        <input type="text" name="name" class="auth-input" placeholder="Your Name" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" class="auth-input" placeholder="Your Email" required>
                                    </div>
                                    <div class="form-group full-width">
                                        <textarea name="comment" class="modern-textarea" rows="3" placeholder="Write your reply..." required></textarea>
                                    </div>
                                    <button type="submit" class="reply-submit-btn" id="reply-btn-{{ $comment->id }}">
                                        Post Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Replies -->
                @if($comment->approvedReplies && $comment->approvedReplies->count() > 0)
                <div class="comment-replies">
                    @foreach($comment->approvedReplies as $reply)
                    <div class="comment-item">
                        <div class="comment-avatar">
                            <div class="comment-avatar-placeholder">{{ strtoupper(substr($reply->name, 0, 1)) }}</div>
                        </div>
                        <div class="comment-content">
                            <div class="comment-meta">
                                <span class="user-name">{{ $reply->name }}</span>
                                <span class="comment-date">{{ $reply->created_at->format('M d, Y') }}</span>
                            </div>
                            <p class="comment-text">{{ $reply->comment }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </section>

        @if($relatedBlogs->count() > 0)
        <hr class="section-divider">
        <div class="related-section">
            <div class="related-header">
                <h4 class="related-title">Keep Reading</h4>
            </div>
            <div class="related-grid">
                @foreach($relatedBlogs as $relatedBlog)
                <a href="{{ route('front.blog.show', $relatedBlog->slug) }}" class="related-card">
                    @if($relatedBlog->featured_image_url)
                        <img src="{{ $relatedBlog->featured_image_url }}" alt="{{ $relatedBlog->title }}" class="related-card-img">
                    @else
                        <img src="https://via.placeholder.com/400x180?text=Blog" alt="{{ $relatedBlog->title }}" class="related-card-img">
                    @endif
                    <div class="related-card-body">
                        <h5>{{ $relatedBlog->title }}</h5>
                        <span class="related-date">{{ $relatedBlog->published_at ? $relatedBlog->published_at->format('M d, Y') : $relatedBlog->created_at->format('M d, Y') }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</article>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reading progress bar
    var progressBar = document.getElementById('readingProgress');
    var articleContent = document.getElementById('articleContent');

    if (progressBar && articleContent) {
        window.addEventListener('scroll', function() {
            var rect = articleContent.getBoundingClientRect();
            var contentTop = rect.top + window.scrollY;
            var contentHeight = articleContent.offsetHeight;
            var scrolled = window.scrollY - contentTop + window.innerHeight * 0.5;
            var progress = Math.min(100, Math.max(0, (scrolled / contentHeight) * 100));
            progressBar.style.width = progress + '%';
        });
    }

    // Comment form
    var form = document.getElementById('blog-comment-form');
    var alertBox = document.getElementById('comment-alert');
    var submitBtn = document.getElementById('comment-submit-btn');
    var blogSlug = '{{ $blog->slug }}';

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var name = form.querySelector('input[name="name"]').value.trim();
        var email = form.querySelector('input[name="email"]').value.trim();
        var comment = form.querySelector('textarea[name="comment"]').value.trim();

        if (!name || !email || !comment) {
            showAlert('Please fill in all fields.', 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/v1/blog/' + blogSlug + '/comment');
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Accept', 'application/json');

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
        }

        xhr.onload = function() {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Post Comment';

            if (xhr.status >= 200 && xhr.status < 300) {
                var response = JSON.parse(xhr.responseText);
                showAlert(response.message || 'Comment submitted successfully!', 'success');
                form.reset();
            } else {
                var errorResponse;
                try {
                    errorResponse = JSON.parse(xhr.responseText);
                } catch(e) {
                    errorResponse = {};
                }

                if (errorResponse.errors) {
                    var messages = [];
                    for (var key in errorResponse.errors) {
                        messages.push(errorResponse.errors[key][0]);
                    }
                    showAlert(messages.join('<br>'), 'error');
                } else {
                    showAlert(errorResponse.message || 'Something went wrong. Please try again.', 'error');
                }
            }
        };

        xhr.onerror = function() {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Post Comment';
            showAlert('Network error. Please try again.', 'error');
        };

        xhr.send(JSON.stringify({
            name: name,
            email: email,
            comment: comment
        }));
    });

    function showAlert(message, type) {
        alertBox.className = 'comment-alert ' + type;
        alertBox.innerHTML = message;
        alertBox.style.display = 'block';

        if (type === 'success') {
            setTimeout(function() {
                alertBox.style.display = 'none';
            }, 5000);
        }
    }

    // Reply form toggle
    window.toggleReplyForm = function(commentId) {
        var replyForm = document.getElementById('reply-form-' + commentId);
        if (replyForm) {
            replyForm.classList.toggle('active');
        }
    };

    // Reply form submission
    window.submitReply = function(e, parentId) {
        e.preventDefault();

        var replyForm = e.target;
        var replyAlert = document.getElementById('reply-alert-' + parentId);
        var replyBtn = document.getElementById('reply-btn-' + parentId);

        var name = replyForm.querySelector('input[name="name"]').value.trim();
        var email = replyForm.querySelector('input[name="email"]').value.trim();
        var comment = replyForm.querySelector('textarea[name="comment"]').value.trim();

        if (!name || !email || !comment) {
            showReplyAlert(replyAlert, 'Please fill in all fields.', 'error');
            return;
        }

        replyBtn.disabled = true;
        replyBtn.textContent = 'Submitting...';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/v1/blog/' + blogSlug + '/comment');
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Accept', 'application/json');

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
        }

        xhr.onload = function() {
            replyBtn.disabled = false;
            replyBtn.textContent = 'Post Reply';

            if (xhr.status >= 200 && xhr.status < 300) {
                var response = JSON.parse(xhr.responseText);
                showReplyAlert(replyAlert, response.message || 'Reply submitted successfully!', 'success');
                replyForm.reset();
            } else {
                var errorResponse;
                try {
                    errorResponse = JSON.parse(xhr.responseText);
                } catch(err) {
                    errorResponse = {};
                }

                if (errorResponse.errors) {
                    var messages = [];
                    for (var key in errorResponse.errors) {
                        messages.push(errorResponse.errors[key][0]);
                    }
                    showReplyAlert(replyAlert, messages.join('<br>'), 'error');
                } else {
                    showReplyAlert(replyAlert, errorResponse.message || 'Something went wrong.', 'error');
                }
            }
        };

        xhr.onerror = function() {
            replyBtn.disabled = false;
            replyBtn.textContent = 'Post Reply';
            showReplyAlert(replyAlert, 'Network error. Please try again.', 'error');
        };

        xhr.send(JSON.stringify({
            name: name,
            email: email,
            comment: comment,
            parent_id: parentId
        }));
    };

    function showReplyAlert(alertEl, message, type) {
        alertEl.className = 'reply-alert ' + type;
        alertEl.innerHTML = message;
        alertEl.style.display = 'block';

        if (type === 'success') {
            setTimeout(function() {
                alertEl.style.display = 'none';
            }, 5000);
        }
    }
});
</script>
@endsection
