<?php

namespace App\Models;

class Comment extends BaseModel
{
    protected $table = 'blog_comments';

    protected $default = ['xid', 'name', 'comment'];

    protected $guarded = ['id'];

    protected $appends = ['xid'];

    protected $hashableGetterFunctions = [
        'getXBlogIdAttribute' => 'blog_id',
        'getXParentIdAttribute' => 'parent_id',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function approvedReplies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('status', 'approved');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
