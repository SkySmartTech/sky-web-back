<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'album_id','title','caption','image','display_order','taken_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
