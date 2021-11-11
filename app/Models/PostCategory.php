<?php

namespace App\Models;

use App\Casts\Json;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
    use SoftDeletes, Sluggable;
    protected $guarded = ['id'];
    protected $casts = ['tags' => Json::class];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function setStatusAttribute($value)
    {
        if ($value == "true")
            $this->attributes['status'] = 1;
        else if ($value == "false")
            $this->attributes['status'] = 0;

    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
