<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'galleryable_id',
        'galleryable_type',
        'type', // 'image' or 'video'
        'url', // storage path or embed URL
        'title',
        'description',
        'is_highlight',
        'sort_order'
    ];

    /**
     * Get the parent galleryable model (Athlete, User, etc).
     */
    public function galleryable()
    {
        return $this->morphTo();
    }
}
