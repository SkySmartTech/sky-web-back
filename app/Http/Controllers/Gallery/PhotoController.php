<?php

namespace App\Http\Controllers\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gallery\StorePhotoRequest;
use App\Http\Requests\Gallery\UpdatePhotoRequest;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{

    public function index(Request $request)
    {
        $q = Photo::query()->with('album');

        if ($albumId = $request->query('album_id')) {
            $q->where('album_id', $albumId);
        }
        if ($s = $request->query('search')) {
            $q->where(function ($qq) use ($s) {
                $qq->where('title', 'like', "%{$s}%")
                   ->orWhere('caption', 'like', "%{$s}%");
            });
        }

        $perPage = (int)($request->query('per_page', 24));
        $perPage = max(1, min(100, $perPage));

        return response()->json(
            $q->orderBy('display_order')->orderByDesc('id')->paginate($perPage)->appends($request->query())
        );
    }

    public function show(Photo $photo)
    {
        return response()->json($photo->load('album'));
    }


    public function store(StorePhotoRequest $request)
    {
        $data = $request->validated();

        $path = $request->file('image')->store('photos', 'public');
        $data['image'] = 'storage/'.$path;

        $photo = Photo::create($data);

        return response()->json(['message' => 'Photo created', 'photo' => $photo], 201);
    }


    public function update(UpdatePhotoRequest $request, Photo $photo)
    {
        $data = $request->validated();

        $oldImg = $photo->image;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('photos', 'public');
            $data['image'] = 'storage/'.$path;

            if ($oldImg && str_starts_with($oldImg, 'storage/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $oldImg));
            }
        }

        $photo->update($data);

        return response()->json(['message' => 'Photo updated', 'photo' => $photo]);
    }


    public function destroy(Photo $photo)
    {
        if ($photo->image && str_starts_with($photo->image, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $photo->image));
        }

        $photo->delete();

        return response()->json(['message' => 'Photo deleted']);
    }
}
