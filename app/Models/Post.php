<?php

namespace App\Models;

use App\Casts\Json;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = ['tags' => Json::class];

    public function setStatusAttribute($value)
    {
        if ($value == 'فعال' or $value == 1)
            $this->attributes['status'] = 1;
        else
            $this->attributes['status'] = 0;

    }


    public function setCommentableAttribute($value)
    {
        if ($value == 'فعال' or $value == 1)
            $this->attributes['commentable'] = 1;
        else
            $this->attributes['commentable'] = 0;

    }


    public function category()
    {
        return $this->belongsTo(PostCategory::class);
    }

    public function getCategoryTextAttribute()
    {
        $category = $this->category()->where('id', $this->category_id)->first();
        return $category->name;
    }

    public function setPublishedAtAttribute($value)
    {
        $published_at = explode('/', $value);
        $v = Verta::getGregorian($published_at[0], $published_at[1], $published_at[2]);
        $date = implode('-', $v);
        $this->attributes['published_at'] = $date;
    }

    public function getPublishedAttribute()
    {
        $v = new Verta($this->published_at);
        return $v->format('Y/m/d');
    }


    public function getPublishedTextAttribute()
    {
        $v = new Verta($this->published_at);
        $now = Verta::now();
        return $v < $now ? 'منتشر شده' : $v->formatDifference();
    }
}
