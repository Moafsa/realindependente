<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryItemController extends Controller
{
    /**
     * Store a newly created gallery item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'galleryable_type' => 'nullable|string',
            'galleryable_id' => 'nullable|integer',
            'type' => 'required|in:image,video',
            'url' => 'required_if:type,video|url|nullable',
            'image' => 'required_if:type,image|image|max:10240|nullable', // 10MB max
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_highlight' => 'boolean',
        ]);

        $item = new GalleryItem();
        $item->tenant_id = tenant('id');
        $item->galleryable_type = $request->galleryable_type;
        $item->galleryable_id = $request->galleryable_id;
        $item->type = $request->type;
        $item->title = $request->title;
        $item->description = $request->description;
        $item->is_highlight = $request->boolean('is_highlight', false);

        if ($request->type === 'image' && $request->hasFile('image')) {
            $path = $request->file('image')->store('gallery', 'public');
            $item->url = $path;
        } elseif ($request->type === 'video') {
            $item->url = $request->url;
        }

        $item->save();

        return redirect()->back()->with('success', 'Mídia adicionada com sucesso!');
    }

    /**
     * Remove the specified gallery item.
     */
    public function destroy(GalleryItem $galleryItem)
    {
        // Check ownership/tenant
        if ($galleryItem->tenant_id !== tenant('id')) {
            abort(403);
        }

        if ($galleryItem->type === 'image' && $galleryItem->url) {
            Storage::disk('public')->delete($galleryItem->url);
        }

        $galleryItem->delete();

        return redirect()->back()->with('success', 'Mídia removida com sucesso!');
    }
}
