<?php

namespace Iltar\HttpBundle\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
class ParameterResolvingRouterTest extends \PHPUnit_Framework_TestCase
{
    public function testDelegation()
    {
        $routes = $this->prophesize(RouteCollection::class);
        $context = $this->prophesize(RequestContext::class);
        $decorated = $this->prophesize(RouterInterface::class)->willImplement(RequestMatcherInterface::class);
        $collection = $this->prophesize(ResolverCollectionInterface::class);
        $request = $this->prophesize(Request::class);

        $decorated->setContext($context)->shouldBeCalled();
        $decorated->getContext()->willReturn($context);
        $decorated->getRouteCollection()->willReturn($routes);
        $decorated->match('/path-matcher')->willReturn(true);
        $decorated->generate('app.route', [], UrlGeneratorInterface::ABSOLUTE_PATH)->willReturn('/returned/path/');
        $decorated->matchRequest($request)->willReturn(true);
        $collection->resolve([])->willReturn([]);

        $router = new ParameterResolvingRouter($decorated->reveal(), $collection->reveal());
        $router->setContext($context->reveal());
        self::assertSame($context->reveal(), $router->getContext());
        self::assertSame($routes->reveal(), $router->getRouteCollection());
        self::assertTrue($router->match('/path-matcher'));
        self::assertTrue($router->matchRequest($request->reveal()));
        self::assertEquals('/returned/path/', $router->generate('app.route'));
        self::assertNull($router->warmUp('/'));
    }

    public function testDelegationForWarmup()
    {
        $collection = $this->prophesize(ResolverCollectionInterface::class);
        $decorated = $this->prophesize(RouterInterface::class)->willImplement(RequestMatcherInterface::class);
        $decorated->willImplement(WarmableInterface::class);
        $decorated->warmUp('/tmp')->shouldBeCalled();

        $router = new ParameterResolvingRouter($decorated->reveal(), $collection->reveal());

        self::assertNull($router->warmUp('/tmp'));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testMissingMatcher()
    {
        $decorated = $this->prophesize(RouterInterface::class);
        $collection = $this->prophesize(ResolverCollectionInterface::class);
        $request = $this->prophesize(Request::class);
        $router = new ParameterResolvingRouter($decorated->reveal(), $collection->reveal());
        self::assertTrue($router->matchRequest($request->reveal()));
    }
}
