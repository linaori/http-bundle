<?php

namespace Iltar\HttpBundle\DoctrineBridge;

use Doctrine\ORM\Mapping\MappingException;
use Iltar\HttpBundle\DoctrineBridge\Exception\CompositePrimaryKeyException;
use Iltar\HttpBundle\DoctrineBridge\Exception\IdentifyingFieldNotReachableException;
use Iltar\HttpBundle\ModelDescriptor\ModelDescriptorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class EntityIdDescriptor implements ModelDescriptorInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @var array
     */
    private $resolvedCache = [];

    /**
     * @param RegistryInterface $registry
     * @param PropertyAccessor  $propertyAccessor
     */
    public function __construct(RegistryInterface $registry, PropertyAccessor $propertyAccessor)
    {
        $this->registry = $registry;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Returns true of the implementation manages this type of object.
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function isManaged($object)
    {
        if (!is_object($object)) {
            return false;
        }

        $className = get_class($object);

        if (isset($this->resolvedCache[$className])) {
            return $this->resolvedCache[$className];
        }

        try {
            $this->registry->getRepository($className);
        } catch (\Exception $e) {
            return $this->resolvedCache[$className] = false;
        } catch (\Throwable $e) {
            return $this->resolvedCache[$className] = false;
        }

        return $this->resolvedCache[$className] = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifyingValue($object)
    {
        $className = get_class($object);

        $em = $this->registry->getManagerForClass($className);
        $metadata = $em->getClassMetadata($className);

        $identifier = $metadata->getIdentifier();

        if (count($identifier) > 1) {
            throw new CompositePrimaryKeyException(sprintf('Composite Primary Keys cannot be resolved to a single scalar value on Entity: %s', get_class($object)));
        }

        try {
            $pkValue = $this->propertyAccessor->getValue($object, $identifier[0]);
        } catch (AccessException $e) {
            throw new IdentifyingFieldNotReachableException(sprintf('The property accessor was unable to access %s.', $identifier[0]));
        }

        if (is_object($pkValue)) {
            $pkValue = $this->getIdentifyingValue($pkValue);
        }

        return $pkValue;
    }
}
