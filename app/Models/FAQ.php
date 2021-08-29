<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FAQ extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'faqs';
    protected $casts = ['tags' => Json::class];


    public function setStatusAttribute($value)
    {
        if ($value == 'فعال' or $value == 1)
            $this->attributes['status'] = 1;
        else
            $this->attributes['status'] = 0;
    }
}
