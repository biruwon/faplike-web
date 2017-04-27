<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Url
{
    /**
     * @Assert\Url()
     */
    private $url;

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }
}
