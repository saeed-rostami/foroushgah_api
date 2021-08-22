<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'body',
        'summary',
        'image',
        'tags',
        'status',
        'commentable',
//        'published_at',
        'category_id'
    ];
    protected $appends = ['status_text', 'commentable_text' , 'category_text'];
    protected $casts = ['tags' => Json::class];

    public function setStatusAttribute($value)
    {
        if ($value == 'فعال')
            $this->attributes['status'] = 1;
        else
            $this->attributes['status'] = 0;

    }

    public function getStatusTextAttribute()
    {
        if ($this->status == 1)
            return 'فعال';
        else
            return 'غیر فعال';
    }

    public function setCommentableAttribute($value)
    {
        if ($value == 'فعال')
            $this->attributes['commentable'] = 1;
        else
            $this->attributes['commentable'] = 0;

    }

    public function getCommentableTextAttribute()
    {
        if ($this->commentable == 1)
            return 'فعال';
        else
            return 'غیر فعال';
    }


    public function category()
    {
        return $this->belongsTo(PostCategory::class);
    }

    public function getCategoryTextAttribute()
    {
        $category = $this->category()->where('id' , $this->category_id)->first();
        return $category->name;
    }
}
