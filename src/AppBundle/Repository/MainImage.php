<?php

namespace AppBundle\Repository;

class MainImage
{
    const DIC = 'doppelganger.repository.main_image';

    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function getByName($name)
    {
        return sprintf('%s/%s/%s', $this->targetDir . '/full', $name, 'main.jpg');
    }
    
    public function getFeaturedPictures($name)
    {
        $featuredImagesPath = $this->targetDir . '/featured';

        $featuredPictures = array_values(array_diff(scandir($featuredImagesPath . DIRECTORY_SEPARATOR . $name), ['..', '.']));
        $featuredImagesPathList = [];
        for ($i = 0; $i<3; $i++) {
            array_push($featuredImagesPathList, sprintf('%s/%s/%s', $featuredImagesPath, $name, $featuredPictures[$i]));
        }

        return $featuredImagesPathList;
    }
}
