<?php

namespace Iltar\HttpBundle\DoctrineBridge;

use Doctrine\ORM\Mapping\MappingException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
class EntityIdResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testArray()
    {
        $manager = $this->prophesize(RegistryInterface::class);
        $accessor = PropertyAccess::createPropertyAccessor();
        $resolver = new EntityIdDescriptor($manager->reveal(), $accessor);

        self::assertFalse($resolver->isManaged(['id' => 400]));
    }

    public function testObjectNotEntity()
    {
        $object = $this->prophesize(TestObject::class);
        $manager = $this->prophesize(RegistryInterface::class);
        $accessor = PropertyAccess::createPropertyAccessor();
        $resolver = new EntityIdDescriptor($manager->reveal(), $accessor);

        $manager->getRepository(get_class($object->reveal()))->shouldBeCalled()->willThrow(new MappingException());

        self::assertFalse($resolver->isManaged($object->reveal()));

        // should hit the cache
        self::assertFalse($resolver->isManaged($object->reveal()));
        self::assertAttributeSame([get_class($object->reveal()) => false], 'resolvedCache', $resolver);
    }

    public function testEntity()
    {
        $entity = $this->prophesize(TestObject::class);
        $manager = $this->prophesize(RegistryInterface::class);
        $accessor = PropertyAccess::createPropertyAccessor();
        $resolver = new EntityIdDescriptor($manager->reveal(), $accessor);

        $manager->getRepository(get_class($entity->reveal()))->shouldBeCalled();

        self::assertTrue($resolver->isManaged($entity->reveal()));
    }
}

interface TestObject
{
}
