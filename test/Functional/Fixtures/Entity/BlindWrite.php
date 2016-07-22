<?php

namespace Iltar\HttpBundle\Functional\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class BlindWrite
{
    /** @ORM\Id() @ORM\Column(type="integer") */
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}
