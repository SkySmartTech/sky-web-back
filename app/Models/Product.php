<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id','title','slug','description','price',
        'image','thumbnail','url',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
