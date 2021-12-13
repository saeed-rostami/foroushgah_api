<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'user_id'];

//    RELATIONS
    public function commentable()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function getFullNameAttribute()
    {
        return $this->author()->name . $this->author()->family;
    }

}
