<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'image_url',
        'status',
        'scheduled_at',
        'published_at',
        'meta_description',
        'ai_model',
        'tokens_used',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
                
                // Ensure slug is unique
                $count = static::where('slug', 'LIKE', "{$post->slug}%")->count();
                if ($count > 0) {
                    $post->slug .= '-' . ($count + 1);
                }
            }
        });
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
                     ->whereNotNull('scheduled_at')
                     ->where('scheduled_at', '>', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Helpers
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at <= now();
    }
}
