<?php

namespace Iltar\HttpBundle\Router\Resolver;

use Iltar\HttpBundle\Router\ParameterResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Resolves anything that's mapped.
 *
 * Arguments are passed via the constructor in an array: [
 *    'App\User' => [
 *      'username'   => 'username',
 *       '_fallback' => 'id',
 *    ,
 *    'App\Post' => ['_fallback' => 'slug'],
 *  ]
 *
 * Uses the Property Accessor to read properties given by importance:
 *   1. If mapped, the direct path
 *   2. If mapped, the _fallback
 *   3. If accessible, the Object.name
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 *
 * @final as of 2.0, when the deprecated MappablePropertyPathResolver will be removed.
 */
/*final*/ class MappablePropertyPathResolver implements ParameterResolverInterface
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @var array
     */
    private $mapping;

    /**
     * @param PropertyAccessor $propertyAccessor
     * @param array            $mapping
     */
    public function __construct(PropertyAccessor $propertyAccessor, array $mapping)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->mapping = $mapping;
    }

    /**
     * Supports anything that's mapped or readable directly.
     *
     * {@inheritdoc}
     */
    public function supportsParameter($name, $object)
    {
        if (!is_object($object)) {
            return false;
        }

        $class = get_class($object);

        return isset($this->mapping[$class][$name])
            || isset($this->mapping[$class]['_fallback'])
            || (is_string($name) && $this->propertyAccessor->isReadable($object, $name));
    }

    /**
     * {@inheritdoc}
     */
    public function resolveParameter($name, $object)
    {
        $class = get_class($object);

        switch (true) {
            case isset($this->mapping[$class][$name]):
                $path = $this->mapping[$class][$name];
                break;
            case isset($this->mapping[$class]['_fallback']):
                $path = $this->mapping[$class]['_fallback'];
                break;
            default:
                $path = $name;
        }

        return (string) $this->propertyAccessor->getValue($object, $path);
    }
}
