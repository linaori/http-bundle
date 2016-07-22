<?php

namespace Iltar\HttpBundle\DoctrineBridge\Router;

use Iltar\HttpBundle\ModelDescriptor\ModelDescriptorInterface;
use Iltar\HttpBundle\Router\Resolver\IdentifyingValueResolver;

/**
 * Uses a model descriptor to get the identifying value of a model.
 *
 * @deprecated as of 1.1 and will be removed in 2.0. Use the Iltar\HttpBundle\Router\Resolver\IdentifyingValueResolver instead.
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class EntityIdResolver extends IdentifyingValueResolver
{
    public function __construct(ModelDescriptorInterface $modelDescriptor)
    {
        @trigger_error(sprintf('%s is deprecated as of 1.1 and will be removed in 2.0. Use the %s instead.', EntityIdResolver::class, IdentifyingValueResolver::class), E_USER_DEPRECATED);

        parent::__construct($modelDescriptor);
    }
}
