<?php

namespace Iltar\HttpBundle\DoctrineBridge\Router;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\MappingException;
use Iltar\HttpBundle\Exception\UncallableMethodException;
use Iltar\HttpBundle\Router\ParameterResolverInterface;

/**
 * A resolver using the ManagerRegistry to call getId if possible.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class EntityIdResolver implements ParameterResolverInterface
{
    /**
     * @var array
     */
    private $resolvedCache = [];

    /**
     * @var ManagerRegistry
     */
    private $manager;

    /**
     * @param ManagerRegistry $manager
     */
    public function __construct(ManagerRegistry $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Supports any value which is an entity.
     *
     * {@inheritdoc}
     */
    public function supportsParameter($name, $value)
    {
        if (!is_object($value)) {
            return false;
        }

        if (isset($this->resolvedCache[get_class($value)])) {
            return $this->resolvedCache[get_class($value)];
        }

        try {
            $this->manager->getRepository(get_class($value));
        } catch (MappingException $e) {
            return $this->resolvedCache[get_class($value)] = false;
        }

        return $this->resolvedCache[get_class($value)] = true;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveParameter($name, $entity)
    {
        $callable = [$entity, 'getId'];

        if (!is_callable($callable)) {
            throw new UncallableMethodException(
                sprintf('Method %s::getId() is expected to be callable for %s.', get_class($entity), self::class)
            );
        }

        return (string) $callable();
    }
}

