<?php

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Image
{
    /**@Assert\Image(
     *     minWidth = 1200,
     *     maxWidth = 1200,
     *     minHeight = 1200,
     *     maxHeight = 1200
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
