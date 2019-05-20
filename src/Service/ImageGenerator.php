<?php

namespace App\Service;

use App\Entity\Image;
use App\Kernel;

class ImageGenerator
{
    public static function getImage($name): Image
    {
        $folder = Kernel::getDir()."/public/uploads/";
        $fileName = uniqid().'.jpg';
        if (!is_dir($folder)) mkdir($folder);
        copy(
            Kernel::getDir()."/public/image/$name",
            $folder.$fileName
        );
        return (new Image())
            ->setAlt($name)
            ->setName($fileName);
    }
}