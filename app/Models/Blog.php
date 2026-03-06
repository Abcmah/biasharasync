<?php

namespace App\Models;

use Illuminate\Support\Str;

class Blog extends BaseModel
{
    protected $table = 'blogs';

    protected $default = ['xid', 'title', 'slug'];

    protected $guarded = ['id'];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = ['xid', 'featured_image_url'];

    public function getFeaturedImageUrlAttribute()
    {
        if ($this->featured_image) {
            return asset('uploads/blogs/' . $this->featured_image);
        }

        return null;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'blog_id');
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class, 'blog_id')->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public static function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $original . '-' . $count++;
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
}
