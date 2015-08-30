<?php
namespace Iltar\HttpBundle\Router;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\Router\MappablePropertyPathResolver
 */
class MappablePropertyPathResolverTest extends \PHPUnit_Framework_TestCase
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

    /** @dataProvider getSupportsData */
    public function testSupports($name, $parameter, $supported = true)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $resolver         = new MappablePropertyPathResolver($propertyAccessor, self::$mapping);

        $this->assertEquals($supported, $resolver->supportsParameter($name, $parameter));
    }

    public function getSupportsData()
    {
        return [
            ['henk', ['henkje' => 'henk'], false],

            ['henk', new UserStub(410, 'henkje')],
            ['username', new UserStub(410, 'henkje')],

            ['jan', new PostStub('henks-post')],
            ['id', new PostStub('henks-post')],

            ['id', new ReplyStub(420, new PostStub('slug'))],
            ['henk', new ReplyStub(420, new PostStub('slug')), false],
        ];
    }

    /** @dataProvider getResolveData */
    public function testResolve($name, $parameter, $resolvedValue)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $resolver = new MappablePropertyPathResolver($propertyAccessor, self::$mapping);

        $this->assertSame($resolvedValue, $resolver->resolveParameter($name, $parameter));
    }

    public function getResolveData()
    {
        return [
            ['fake key', new UserStub(420, 'janalleman'), '420'],
            ['username', new UserStub(50, 'henk'), 'henk'],

            ['any key', new PostStub('henks-slug'), 'henks-slug'],
            ['id', new PostStub('henks-slug'), 'henks-slug'],

            ['id', new ReplyStub(420, new PostStub('slug2')), '420'],
            ['slug', new ReplyStub(420, new PostStub('a-slug')), 'a-slug'],
        ];
    }

    /** @dataProvider getUnmappedSupportsData */
    public function testUnmappedSupports($name, $parameter, $supported = true)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $resolver         = new MappablePropertyPathResolver($propertyAccessor, []);

        $this->assertEquals($supported, $resolver->supportsParameter($name, $parameter));
    }

    public function getUnmappedSupportsData()
    {
        return [
            ['id', new UserSTub(410, 'henkje')],
            ['username', new UserStub(410, 'henkje')],
            ['henk', new UserStub(410, 'henkje'), false],
        ];
    }

    /** @dataProvider getUnmappedResolveData */
    public function testUnmappedResolve($name, $parameter, $resolvedValue)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $resolver = new MappablePropertyPathResolver($propertyAccessor, []);

        $this->assertSame($resolvedValue, $resolver->resolveParameter($name, $parameter));
    }

    public function getUnmappedResolveData()
    {
        return [
            ['id', new UserStub(420, 'janalleman'), '420'],
            ['username', new UserStub(50, 'henk'), 'henk'],
        ];
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
