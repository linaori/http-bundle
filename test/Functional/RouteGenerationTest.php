<?php

namespace Iltar\HttpBundle;

use Iltar\HttpBundle\Functional\Fixtures\Entity\Authentication;
use Iltar\HttpBundle\Functional\Fixtures\Entity\BlindWrite;
use Iltar\HttpBundle\Functional\Fixtures\Entity\Client;
use Iltar\HttpBundle\Functional\Fixtures\Entity\Message;
use Iltar\HttpBundle\Functional\Fixtures\Model\MappedPost;
use Iltar\HttpBundle\Functional\Fixtures\Model\Post;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class RouteGenerationTest extends KernelTestCase
{
    protected function setUp()
    {
        static::bootKernel();
    }

    /** @dataProvider getPropertyPathResolverData */
    public function testPropertyPathResolver($routeName, $routeParameters, $expectedPath)
    {
        $router = static::$kernel->getContainer()->get('router');

        self::assertEquals($expectedPath, $router->generate($routeName, $routeParameters));
    }

    public function getPropertyPathResolverData()
    {
        return [
            ['property_route', ['title' => new Post('hello')], '/post/hello'],
            ['method_route', ['slug' => new Post('A nice title')], '/post/a-nice-title'],
            ['mapped_property_route', ['title_slug' => new MappedPost('Hello World')], '/post/hello-world'],
            ['mapped_fallback_route', ['post' => new MappedPost('HTTPBundle', 12)], '/post/12'],
        ];
    }

    /** @dataProvider getEntityIdResolverData */
    public function testEntityIdResolver($routeName, $routeParameters, $expectedPath)
    {
        $router = static::$kernel->getContainer()->get('router');

        self::assertEquals($expectedPath, $router->generate($routeName, $routeParameters));
    }

    public function getEntityIdResolverData()
    {
        return [
            ['client_route', ['client' => new Client(10)], '/client/10'],
            ['authentication_route', ['authentication' => new Authentication(new Client(500))], '/authentication/500'],
        ];
    }

    /** @expectedException \Iltar\HttpBundle\DoctrineBridge\Exception\CompositePrimaryKeyException */
    public function testEntityIdResolverWithCompositePk()
    {
        $router = static::$kernel->getContainer()->get('router');

        $router->generate('message_route', ['message' => new Message(10, 20)]);
    }

    /** @expectedException \Iltar\HttpBundle\DoctrineBridge\Exception\IdentifyingFieldNotReachableException */
    public function testEntityIdResolverWithWithoutAvailableAccessor()
    {
        $router = static::$kernel->getContainer()->get('router');

        $router->generate('message_route', ['blind_write' => new BlindWrite(10)]);
    }

    public function testIfUnresolvedValuesAreLeftAlone()
    {
        $router = static::$kernel->getContainer()->get('router');

        self::assertSame('/post/10?empty_key=&normal_key=10', $router->generate('mapped_fallback_route', [
            'object_key' => new \stdClass(),
            'empty_key' => '',
            'array_key' => [],
            'normal_key' => '10',
            'resource_key' => fopen(__FILE__, 'rb'),
            'post' => 10,
        ]));
    }

    /**
     * @dataProvider getUnresolvedData
     *
     * @expectedException \Iltar\HttpBundle\Exception\UnresolvedParameterException
     * @expectedExceptionMessage Parameters for the route 'client_route' could not be resolved to scalar values. Parameters: "client".
     */
    public function testIfUnresolvedDataThrowsException($id)
    {
        static::$kernel->getContainer()->get('router')->generate('client_route', ['client' => new Client($id)]);
    }

    public function getUnresolvedData()
    {
        return [[''], [[]], [null], [['non empty' => 'array value']]];
    }

    public function testExpectHttpUtilsToWork()
    {
        $router = static::$kernel->getContainer()->get('router');
        $utils = new HttpUtils($router);
        $request = Request::create('/');
        $request->attributes->set('_route_params', []);
        $request->attributes->set('foobar', new \stdClass());

        self::assertSame('http://localhost/login', $utils->generateUri($request, 'login'));
    }
}
