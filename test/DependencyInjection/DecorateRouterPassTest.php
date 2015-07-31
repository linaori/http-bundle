<?php
namespace Iltar\HttpBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\DependencyInjection\DecorateRouterPass
 */
class DecorateRouterPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $pass      = new DecorateRouterPass();

        $pass->process($container);

        $this->assertTrue($container->has('iltar.http.parameter_resolving_router'));
    }
}
