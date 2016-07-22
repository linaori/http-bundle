<?php

namespace Iltar\HttpBundle\Router\Resolver;

use Iltar\HttpBundle\ModelDescriptor\ModelDescriptorInterface;
use Iltar\HttpBundle\Router\ParameterResolverInterface;

/**
 * Uses a model descriptor to get the identifying value of a model.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 *
 * @final as of 2.0, when the EntityIdResolver will be removed.
 */
/*final*/ class IdentifyingValueResolver implements ParameterResolverInterface
{
    /**
     * @var ModelDescriptorInterface
     */
    private $modelDescriptor;

    /**
     * @param ModelDescriptorInterface $modelDescriptor
     */
    public function __construct(ModelDescriptorInterface $modelDescriptor)
    {
        $this->modelDescriptor = $modelDescriptor;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsParameter($name, $value)
    {
        return $this->modelDescriptor->isManaged($value);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveParameter($name, $object)
    {
        return $this->modelDescriptor->getIdentifyingValue($object);
    }
}
