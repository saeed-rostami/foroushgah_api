<?php
/**
 * Created by PhpStorm.
 * User: Saeed
 * Date: 9/3/2021
 * Time: 5:26 PM
 */

namespace App\Services;

use Intervention\Image\Facades\Image;


class ImageIntervention
{
    public static function Resize($path, $name, $width, $height)
    {
        $img = Image::make($path . $name)->resize($width, $height);
        $img->save();
    }
}
