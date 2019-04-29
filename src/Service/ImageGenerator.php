<?php

namespace App\Service;

use App\Entity\Image;
use App\Kernel;

class ImageGenerator
{
    public static function getImage($name): Image
    {
        $fileName = uniqid().'.jpg';
        copy(
            Kernel::getDir()."/public/image/$name",
            Kernel::getDir()."/public/uploads/$fileName"
        );
        return (new Image())
            ->setAlt($name)
            ->setName($fileName);
    }
}