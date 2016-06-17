<?php

namespace Iltar\HttpBundle;

use Iltar\HttpBundle\Functional\Fixtures\Model\MappedPost;
use Iltar\HttpBundle\Functional\Fixtures\Model\Post;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
}
