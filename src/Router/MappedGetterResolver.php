<?php
namespace Iltar\HttpBundle\Router;

use Iltar\HttpBundle\Exception\UncallableMethodException;

/**
 * Resolves anything that's mapped.
 *
 * Example:
 *  - App\User.username : getUsername # grab the username if the key is username
 *  - App\User          : getid       # Grab getId if nothing more specific is defined
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
     * @var array
     */
    private $mapping;

    /**
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
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
        $part     = $this->mapping[get_class($entity)];
        $method   = isset($part[$name]) ? $part[$name] : $part['_fallback'];
        $callable = [$entity, $method];

        if (!is_callable($callable)) {
            throw new UncallableMethodException(
                sprintf('Method %s::%s is expected to be callable for %s.', get_class($entity), $method, self::class)
            );
        }

        return (string) $callable();
    }
}
