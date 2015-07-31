<?php
namespace Iltar\HttpBundle\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\Router\ParameterResolvingRouter
 */
class ParameterResolvingRouterTest extends \PHPUnit_Framework_TestCase
{
    public function testDelegation()
    {
        $routes     = $this->prophesize(RouteCollection::class);
        $context    = $this->prophesize(RequestContext::class);
        $decorated  = $this->prophesize(RouterInterface::class)->willImplement(RequestMatcherInterface::class);
        $collection = $this->prophesize(ResolverCollectionInterface::class);
        $request    = $this->prophesize(Request::class);

        $decorated->setContext($context)->shouldBeCalled();
        $decorated->getContext()->willReturn($context);
        $decorated->getRouteCollection()->willReturn($routes);
        $decorated->match('/path-matcher')->willReturn(true);
        $decorated->generate('app.route', [], UrlGeneratorInterface::ABSOLUTE_PATH)->willReturn('/returned/path/');
        $decorated->matchRequest($request)->willReturn(true);
        $collection->resolve([])->willReturn([]);

        $router = new ParameterResolvingRouter($decorated->reveal(), $collection->reveal());
        $router->setContext($context->reveal());
        $this->assertSame($context->reveal(), $router->getContext());
        $this->assertSame($routes->reveal(), $router->getRouteCollection());
        $this->assertTrue($router->match('/path-matcher'));
        $this->assertTrue($router->matchRequest($request->reveal()));
        $this->assertEquals('/returned/path/', $router->generate('app.route'));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testMissingMatcher()
    {
        $decorated  = $this->prophesize(RouterInterface::class);
        $collection = $this->prophesize(ResolverCollectionInterface::class);
        $request    = $this->prophesize(Request::class);
        $router     = new ParameterResolvingRouter($decorated->reveal(), $collection->reveal());
        $this->assertTrue($router->matchRequest($request->reveal()));
    }
}
