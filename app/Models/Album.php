<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use SoftDeletes;

    protected $fillable = ['title','slug','description','cover_image'];

    public function photos()
    {
        return $this->hasMany(Photo::class)->orderBy('display_order')->orderByDesc('id');
    }
}
