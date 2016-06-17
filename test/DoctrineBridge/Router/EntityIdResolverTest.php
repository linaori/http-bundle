<?php

namespace Iltar\HttpBundle\DoctrineBridge\Router;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\MappingException;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\DoctrineBridge\Router\EntityIdResolver
 */
class EntityIdResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testArray()
    {
        $manager = $this->prophesize(ManagerRegistry::class);
        $resolver = new EntityIdResolver($manager->reveal());

        self::assertFalse($resolver->supportsParameter('user', ['id' => 400]));
    }

    public function testObjectNotEntity()
    {
        $manager = $this->prophesize(ManagerRegistry::class);
        $object = $this->prophesize(TestObject::class);
        $resolver = new EntityIdResolver($manager->reveal());

        $manager->getRepository(get_class($object->reveal()))->shouldBeCalled()->willThrow(new MappingException());

        self::assertFalse($resolver->supportsParameter('user', $object->reveal()));

        // should hit the cache
        self::assertFalse($resolver->supportsParameter('user', $object->reveal()));
        self::assertAttributeSame([get_class($object->reveal()) => false], 'resolvedCache', $resolver);
    }

    /**
     * @expectedException \Iltar\HttpBundle\Exception\UncallableMethodException
     */
    public function testEntityWithoutId()
    {
        $manager = $this->prophesize(ManagerRegistry::class);
        $entity = new \stdClass();
        $resolver = new EntityIdResolver($manager->reveal());

        $manager->getRepository(get_class($entity))->shouldBeCalled();

        self::assertTrue($resolver->supportsParameter('user', $entity));
        $resolver->resolveParameter('user', $entity);
    }

    public function testEntityWithId()
    {
        $manager = $this->prophesize(ManagerRegistry::class);
        $entity = $this->prophesize(TestEntityWithId::class);
        $resolver = new EntityIdResolver($manager->reveal());

        $manager->getRepository(get_class($entity->reveal()))->shouldBeCalled();
        $entity->getId()->willReturn(420);

        self::assertTrue($resolver->supportsParameter('user', $entity->reveal()));
        self::assertSame('420', $resolver->resolveParameter('user', $entity->reveal()));

        // should hit the cache
        self::assertTrue($resolver->supportsParameter('user', $entity->reveal()));
        self::assertAttributeSame([get_class($entity->reveal()) => true], 'resolvedCache', $resolver);
    }
}

interface TestObject
{
}

interface TestEntityWithId
{
    public function getId();
}
