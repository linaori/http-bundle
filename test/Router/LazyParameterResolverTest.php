<?php
namespace Iltar\HttpBundle\Router;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\Router\LazyParameterResolver
 */
class LazyParameterResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testDelegation()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $delegatee = $this->prophesize(ParameterResolverInterface::class);

        $container->get('router.parameter_resolver.user_resolver.inner')->willReturn($delegatee);
        $delegatee->supportsParameter('user', 42)->willReturn(true);
        $delegatee->resolveParameter('user', 42)->willReturn('henk');

        $resolver = new LazyParameterResolver($container->reveal(), 'router.parameter_resolver.user_resolver.inner');

        $this->assertTrue($resolver->supportsParameter('user', 42));
        $this->assertEquals('henk', $resolver->resolveParameter('user', 42));
    }
}
