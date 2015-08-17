<?php
namespace Iltar\HttpBundle\Router;

use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Resolves anything that's mapped.
 *
 * Example:
 *  - App\User.username : getUsername # grab the username if the key is username
 *  - App\User          : getId       # Grab getId if nothing more specific is defined
 *  - App\Post          : getSlug     # Always grab getSlug
 *
 * Arguments are passed via the constructor in an array: [
 *    ['App\User' => [
 *      'username'   => 'getUsername',
 *       '_fallback' => 'getId',
 *    ],
 *    'App\Post' => ['_fallback' => 'getSlug'],
 *  ]
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
class MappedGetterResolver implements ParameterResolverInterface
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
        $this->mapping          = $mapping;
    }

    /**
     * Supports anything that's mapped.
     *
     * {@inheritdoc}
     */
    public function supportsParameter($name, $entity)
    {
        $part = $this->mapping[get_class($entity)];
        return isset($part[$name]) || isset($part['_fallback']);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveParameter($name, $entity)
    {
        $part = $this->mapping[get_class($entity)];
        $path = isset($part[$name]) ? $part[$name] : $part['_fallback'];

        return (string) $this->propertyAccessor->getValue($entity, $path);
    }
}
