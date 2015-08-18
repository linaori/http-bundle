<?php
namespace Iltar\HttpBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\DependencyInjection\DecorateRouterPass
 */
class DecorateRouterPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        (new XmlFileLoader($this->container, new FileLocator(__DIR__.'/../../src/Resources/config')))
            ->load('router.xml');
    }

    public function testProcessNotEnabled()
    {
        $pass      = new DecorateRouterPass();
        $container = new ContainerBuilder();
        $container
            ->register('app.henk')
            ->setClass('stdClass')
            ->addTag('router.parameter_resolver');

        $pass->process($container);
        $this->assertFalse($container->has('iltar_http.parameter_resolving_router'));
    }

    public function testProcessNoResolvers()
    {
        $pass = new DecorateRouterPass();

        $this->container->setParameter('iltar_http.router.enabled', true);
        $this->container
            ->register('iltar_http.router.parameter_resolver_collection')
            ->setArguments([[]])
            ->setClass('stdClass');

        $pass->process($this->container);

        $this->assertTrue($this->container->has('iltar_http.parameter_resolving_router'));
    }

    public function testProcessMissingPriorityTag()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $pass = new DecorateRouterPass();

        $this->container->setParameter('iltar_http.router.enabled', true);
        $this->container
            ->register('app.henk')
            ->setClass('stdClass')
            ->addTag('router.parameter_resolver');

        $pass->process($this->container);

        $this->assertTrue($this->container->has('iltar_http.parameter_resolving_router'));
        $this->assertTrue($this->container->has('iltar_http.parameter_resolver.app.henk'));
    }

    public function testProcess()
    {
        $pass = new DecorateRouterPass();

        $this->container->setParameter('iltar_http.router.enabled', true);
        $this->container
            ->register('app.henk_150')
            ->setClass('stdClass')
            ->addTag('router.parameter_resolver', ['priority' => 150]);

        $this->container
            ->register('app.henk_50')
            ->setClass('stdClass')
            ->addTag('router.parameter_resolver', ['priority' => 50]);

        $this->container
            ->register('app.henk_100')
            ->setClass('stdClass')
            ->addTag('router.parameter_resolver', ['priority' => 100]);

        $this->container
            ->register('app.henk2_50')
            ->setClass('stdClass')
            ->addTag('router.parameter_resolver', ['priority' => 50]);

        $pass->process($this->container);

        $expectedResolvers = [
            'iltar_http.router.mapped_getters',
            'app.henk_150',
            'app.henk_100',
            'app.henk2_50',
            'app.henk_50',
        ];

        $resolvers = $this->container->getDefinition('iltar_http.router.parameter_resolver_collection')->getArgument(0);

        foreach ($expectedResolvers as $i => $resolver) {
            $this->assertSame($resolver, $resolvers[$i]->__toString());
        }
    }
}
