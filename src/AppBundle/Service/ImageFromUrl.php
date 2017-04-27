<?php


namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFromUrl
{
    const DIC = 'doppelganger.service.image_from_url';

    public function getImage($imageUrl)
    {
        // any security problem here?
        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
        //$temp_file = tempnam(sys_get_temp_dir(), 'Tux');
        //$imageContent = imagecreatefromstring(file_get_contents($imageUrl));

        // copy to a tmp location
        $tmp = sys_get_temp_dir().'/'.sha1(uniqid(mt_rand(), true)).'.'.$extension;
        copy($imageUrl, $tmp);

        $uploadedFile = $this->createUploadedFile($tmp);

        return $uploadedFile;
    }


    private function createUploadedFile($value)
    {
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
