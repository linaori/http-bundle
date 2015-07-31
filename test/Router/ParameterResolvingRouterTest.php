<?php
namespace Iltar\HttpBundle\Router;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
        $context    = $this->prophesize(RequestContext::class);
        $decorated  = $this->prophesize(RouterInterface::class);
        $collection = $this->prophesize(RouteCollection::class);

        $decorated->setContext($context)->shouldBeCalled();
        $decorated->getContext()->willReturn($context);
        $decorated->getRouteCollection()->willReturn($collection);
        $decorated->match('/path-matcher')->willReturn(true);
        $decorated->generate('app.route', [], UrlGeneratorInterface::ABSOLUTE_PATH)->willReturn('/returned/path/');

        $router = new ParameterResolvingRouter($decorated->reveal());
        $router->setContext($context->reveal());
        $this->assertSame($context->reveal(), $router->getContext());
        $this->assertSame($collection->reveal(), $router->getRouteCollection());
        $this->assertTrue($router->match('/path-matcher'));
        $this->assertEquals('/returned/path/', $router->generate('app.route'));
    }
}
