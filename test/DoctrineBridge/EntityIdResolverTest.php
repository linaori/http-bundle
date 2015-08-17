<?php
namespace Iltar\HttpBundle\DoctrineBridge;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\MappingException;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\DoctrineBridge\EntityIdResolver
 */
class EntityIdResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testArray()
    {
        $manager  = $this->prophesize(ManagerRegistry::class);
        $resolver = new EntityIdResolver($manager->reveal());

        $this->assertFalse($resolver->supportsParameter('user', ['id' => 400]));
    }

    public function testObjectNotEntity()
    {
        $manager  = $this->prophesize(ManagerRegistry::class);
        $object   = $this->prophesize(TestObject::class);
        $resolver = new EntityIdResolver($manager->reveal());

        $manager->getRepository(get_class($object->reveal()))->shouldBeCalled()->willThrow(new MappingException());

        $this->assertFalse($resolver->supportsParameter('user', $object->reveal()));

        // should hit the cache
        $this->assertFalse($resolver->supportsParameter('user', $object->reveal()));
        $this->assertAttributeSame([get_class($object->reveal()) => false], 'resolvedCache', $resolver);
    }

    /**
     * @expectedException \Iltar\HttpBundle\Exception\UncallableMethodException
     */
    public function testEntityWithoutId()
    {
        $manager  = $this->prophesize(ManagerRegistry::class);
        $entity   = new \stdClass();
        $resolver = new EntityIdResolver($manager->reveal());

        $manager->getRepository(get_class($entity))->shouldBeCalled();

        $this->assertTrue($resolver->supportsParameter('user', $entity));
        $resolver->resolveParameter('user', $entity);
    }

    public function testEntityWithId()
    {
        $manager  = $this->prophesize(ManagerRegistry::class);
        $entity   = $this->prophesize(TestEntityWithId::class);
        $resolver = new EntityIdResolver($manager->reveal());

        $manager->getRepository(get_class($entity->reveal()))->shouldBeCalled();
        $entity->getId()->willReturn(420);

        $this->assertTrue($resolver->supportsParameter('user', $entity->reveal()));
        $this->assertSame('420', $resolver->resolveParameter('user', $entity->reveal()));

        // should hit the cache
        $this->assertTrue($resolver->supportsParameter('user', $entity->reveal()));
        $this->assertAttributeSame([get_class($entity->reveal()) => true], 'resolvedCache', $resolver);
    }
}

interface TestObject
{
}

interface TestEntityWithId
{
    public function getId();
}
