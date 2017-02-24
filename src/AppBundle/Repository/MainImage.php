<?php

namespace AppBundle\Repository;

class MainImage
{
    const DIC = 'dopplerganger.repository.main_image';

    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function getByName($name)
    {
        return sprintf('%s/%s/%s', $this->targetDir, $name, 'pic.jpg');
    }
}
