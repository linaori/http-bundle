<?php

namespace Iltar\HttpBundle\ModelDescriptor;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
interface ModelDescriptorInterface
{
    /**
     * Returns true of the implementation manages this type of object.
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function isManaged($object);

    /**
     * @param mixed $object The object to find the id value of.
     *
     * @return mixed Either the id or an object holding a possible id.
     */
    public function getIdentifyingValue($object);
}
