<?php

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Image
{
    /**
     * @Assert\Image(
     *     minWidth = 80,
     *     maxWidth = 1600,
     *     minHeight = 80,
     *     maxHeight = 2000,
     *     detectCorrupted = true
     * )
     */
    private $image;

    public function getImage()
    {
        return $this->image;
    }

    public function setImage(File $image)
    {
        $this->image = $image;
    }
}
