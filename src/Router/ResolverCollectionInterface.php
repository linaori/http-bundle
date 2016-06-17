<?php

namespace Iltar\HttpBundle\Router;

/**
 * @author Iltar van der Berg <kjaril@gmail.com>
 */
interface ResolverCollectionInterface
{
    /**
     * Resolves a set of parameters.
     *
     * @param array $parameters to be resolved
     *
     * @return array
     */
    public function resolve(array $parameters);
}
