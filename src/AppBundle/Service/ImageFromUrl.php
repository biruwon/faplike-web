<?php


namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFromUrl
{
    const DIC = 'faplike.service.image_from_url';

    public function getImage($imageUrl)
    {
        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);

        // copy to a tmp location
        $tmp = sys_get_temp_dir().'/'.sha1(uniqid(mt_rand(), true)).'.'.$extension;
        $success = @copy($imageUrl, $tmp);
        
        return $success ? $this->createUploadedFile($tmp) : null;
    }

    private function createUploadedFile($value)
    {
        //@TODO: this is not needed
        if (null !== $value && is_readable($value)) {
            $error = UPLOAD_ERR_OK;
            $size = filesize($value);
            $info = pathinfo($value);
            $name = $info['basename'];

        } else {
            $error = UPLOAD_ERR_NO_FILE;
            $size = 0;
            $name = '';
            $value = '';
        }

        $file = new File($value, true);

        return $file;
    }
}
