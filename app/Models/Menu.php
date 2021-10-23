<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];


    public function setStatusAttribute($value)
    {
        if ($value == "true")
            $this->attributes['status'] = 1;
        else if ($value == "false")
            $this->attributes['status'] = 0;
    }

    public function setParentIdAttribute($value)
    {
        $parentID = $this->where('name', $value)->first();
        if ($parentID)
            $this->attributes['parent_id'] = $parentID->id;
        else
            $this->attributes['parent_id'] = null;
    }


    public function mainMenu()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function subMenus()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

}
