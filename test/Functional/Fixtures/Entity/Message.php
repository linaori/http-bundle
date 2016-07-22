<?php

namespace Iltar\HttpBundle\Functional\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class Message
{
    /** @ORM\Id() @ORM\Column(type="integer") */
    private $from;

    /** @ORM\Id() @ORM\Column(type="integer") */
    private $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }
}
