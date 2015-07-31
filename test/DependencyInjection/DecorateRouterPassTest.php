<?php
namespace Iltar\HttpBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\DependencyInjection\DecorateRouterPass
 */
class DecorateRouterPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessNoResolvers()
    {
        $container = new ContainerBuilder();
        $pass      = new DecorateRouterPass();

        $container
            ->register('iltar.http.parameter_resolver_collection')
            ->setClass('stdClass');

        $pass->process($container);

        $this->assertFalse($container->has('iltar.http.parameter_resolving_router'));
    }

    public function testProcess()
    {
        $container = new ContainerBuilder();
        $pass      = new DecorateRouterPass();

        $container
            ->register('iltar.http.parameter_resolver_collection')
            ->setClass('stdClass');

        $container
            ->register('app.henk')
            ->setClass('stdClass')
            ->addTag('router.parameter_resolver');

        $pass->process($container);

        $this->assertTrue($container->has('iltar.http.parameter_resolving_router'));
        $this->assertTrue($container->has('iltar.http.parameter_resolving.app.henk'));
    }
}
