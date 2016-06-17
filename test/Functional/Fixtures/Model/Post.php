<?php

namespace Iltar\HttpBundle\Functional\Fixtures\Model;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class Post
{
    private $title;
    private $id;

    public function __construct($title, $id = null)
    {
        $this->title = $title;
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSlug()
    {
        return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($this->title)), '-');
    }
}
