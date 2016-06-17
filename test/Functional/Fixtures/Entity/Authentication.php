<?php

namespace Iltar\HttpBundle\Functional\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class Authentication
{
    /**
     * @ORM\Id()
     * @ORM\OneToOne(targetEntity="Client")
     * @ORM\JoinColumn()
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /** @return Client */
    public function getClient()
    {
        return $this->client;
    }
}
