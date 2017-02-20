<?php

namespace AppBundle\Entity;

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

    private $url;

    public function getImage()
    {
        return $this->image;
    }

    public function setImage(UploadedFile $image)
    {
        $this->image = $image;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }
}
