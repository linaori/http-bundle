<?php
namespace Iltar\HttpBundle\Router;

use Iltar\HttpBundle\Exception\UncallableMethodException;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\Router\MappedGetterResolver
 */
class MappedGetterResolverTest extends \PHPUnit_Framework_TestCase
{
    private static $mapping = [
        UserStub::class => [
            'username'  => 'getUsername',
            '_fallback' => 'getId',
        ],
        PostStub::class => [
            '_fallback' => 'getSlug',
        ],
        ReplyStub::class => [
            'id' => 'getId',
        ],
    ];

    public function testSupports()
    {
        $resolver = new MappedGetterResolver(self::$mapping);

        $this->assertTrue($resolver->supportsParameter('henk', new UserStub(410, 'henkje')));
        $this->assertTrue($resolver->supportsParameter('username', new UserStub(410, 'henkje')));

        $this->assertTrue($resolver->supportsParameter('jan', new PostStub('henks-post')));
        $this->assertTrue($resolver->supportsParameter('id', new PostStub('henks-post')));

        $this->assertTrue($resolver->supportsParameter('id', new ReplyStub(420)));
        $this->assertFalse($resolver->supportsParameter('henk', new ReplyStub(420)));
    }

    public function testResolve()
    {
        $resolver = new MappedGetterResolver(self::$mapping);

        $this->assertSame('420', $resolver->resolveParameter('fake key', new UserStub(420, 'janalleman')));
        $this->assertSame('henk', $resolver->resolveParameter('username', new UserStub(50, 'henk')));

        $this->assertSame('henks-slug', $resolver->resolveParameter('any key', new PostStub('henks-slug')));
        $this->assertSame('henks-slug', $resolver->resolveParameter('id', new PostStub('henks-slug')));

        $this->assertSame('420', $resolver->resolveParameter('id', new ReplyStub('420')));
    }

    public function testResolveUncallable()
    {
        $this->setExpectedException(UncallableMethodException::class);

        (new MappedGetterResolver([UserStub::class => ['_fallback' => 'getSlug']]))
            ->resolveParameter('slug', new UserStub(4200, 'henkje'));
    }
}

class UserStub
{
    private $id;
    private $username;
    public function __construct($id, $username)
    {
        $this->id       = $id;
        $this->username = $username;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }
}

class PostStub
{
    private $slug;
    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }
}

class ReplyStub
{
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
