<?php

namespace App\Http\Controllers\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gallery\StoreAlbumRequest;
use App\Http\Requests\Gallery\UpdateAlbumRequest;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AlbumController extends Controller
{

    public function index(Request $request)
    {
        $q = Album::query()->withCount('photos');

        if ($s = $request->query('search')) {
            $q->where(function ($qq) use ($s) {
                $qq->where('title', 'like', "%{$s}%")
                   ->orWhere('slug', 'like', "%{$s}%")
                   ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $perPage = (int)($request->query('per_page', 12));
        $perPage = max(1, min(100, $perPage));

        return response()->json(
            $q->latest()->paginate($perPage)->appends($request->query())
        );
    }


    public function show(Album $album)
    {
        return response()->json(
            $album->load(['photos' => fn($p) => $p->orderBy('display_order')->orderByDesc('id')])
        );
    }


    public function store(StoreAlbumRequest $request)
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
            $base = $data['slug']; $i = 2;
            while (Album::where('slug', $data['slug'])->exists()) {
                $data['slug'] = "{$base}-{$i}";
                $i++;
            }
        }

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('albums', 'public');
            $data['cover_image'] = 'storage/'.$path;
        }

        $album = Album::create($data);

        return response()->json(['message' => 'Album created', 'album' => $album], 201);
    }


    public function update(UpdateAlbumRequest $request, Album $album)
    {
        $data = $request->validated();

        $oldCover = $album->cover_image;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('albums', 'public');
            $data['cover_image'] = 'storage/'.$path;

            if ($oldCover && str_starts_with($oldCover, 'storage/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldCover));
            }
        }

        $album->update($data);

        return response()->json(['message' => 'Album updated', 'album' => $album]);
    }


    public function destroy(Album $album)
    {
        if ($album->cover_image && str_starts_with($album->cover_image, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $album->cover_image));
        }

        $album->delete();

        return response()->json(['message' => 'Album deleted']);
    }
}
