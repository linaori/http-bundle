<?php
namespace Iltar\HttpBundle\Router;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\Router\MappedGetterResolver
 */
class MappedGetterResolverTest extends \PHPUnit_Framework_TestCase
{
    private static $mapping = [
        UserStub::class => [
            'username'  => 'username',
            '_fallback' => 'id',
        ],
        PostStub::class => [
            '_fallback' => 'slug',
        ],
        ReplyStub::class => [
            'id'   => 'id',
            'slug' => 'post.slug',
        ],
    ];

    public function testSupports()
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $resolver         = new MappedGetterResolver($propertyAccessor, self::$mapping);

        $this->assertTrue($resolver->supportsParameter('henk', new UserStub(410, 'henkje')));
        $this->assertTrue($resolver->supportsParameter('username', new UserStub(410, 'henkje')));

        $this->assertTrue($resolver->supportsParameter('jan', new PostStub('henks-post')));
        $this->assertTrue($resolver->supportsParameter('id', new PostStub('henks-post')));

        $this->assertTrue($resolver->supportsParameter('id', new ReplyStub(420, new PostStub('slug'))));
        $this->assertFalse($resolver->supportsParameter('henk', new ReplyStub(420, new PostStub('slug'))));
    }

    public function testResolve()
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $resolver         = new MappedGetterResolver($propertyAccessor, self::$mapping);

        $this->assertSame('420', $resolver->resolveParameter('fake key', new UserStub(420, 'janalleman')));
        $this->assertSame('henk', $resolver->resolveParameter('username', new UserStub(50, 'henk')));

        $this->assertSame('henks-slug', $resolver->resolveParameter('any key', new PostStub('henks-slug')));
        $this->assertSame('henks-slug', $resolver->resolveParameter('id', new PostStub('henks-slug')));

        $this->assertSame('420', $resolver->resolveParameter('id', new ReplyStub(420, new PostStub('slug2'))));
        $this->assertSame('a-slug', $resolver->resolveParameter('slug', new ReplyStub(420, new PostStub('a-slug'))));
    }

    public function testUnmapped()
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $resolver         = new MappedGetterResolver($propertyAccessor, []);

        $this->assertTrue($resolver->supportsParameter('id', new UserStub(410, 'henkje')));
        $this->assertTrue($resolver->supportsParameter('username', new UserStub(410, 'henkje')));
        $this->assertFalse($resolver->supportsParameter('henk', new UserStub(410, 'henkje')));

        $this->assertSame('420', $resolver->resolveParameter('id', new UserStub(420, 'janalleman')));
        $this->assertSame('henk', $resolver->resolveParameter('username', new UserStub(50, 'henk')));
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

    public function getTitle()
    {
        return $this->title;
    }
}

class ReplyStub
{
    private $id;
    private $post;
    public function __construct($id, PostStub $post)
    {
        $this->id   = $id;
        $this->post = $post;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPost()
    {
        return $this->post;
    }
}
