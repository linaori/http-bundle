<?php
namespace Iltar\HttpBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\DependencyInjection\Configuration
 * @covers Iltar\HttpBundle\DependencyInjection\IltarHttpExtension
 */
class IltarHttpExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadWithRouter()
    {
        $ext       = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load([], $container);

        $this->assertTrue($container->has('iltar.http.router.parameter_resolver_collection'));
        $this->assertTrue($container->hasParameter('iltar.http.router.enabled'));
        $this->assertFalse($container->hasParameter('iltar.http.router.entity_id_resolver.enabled'));
    }

    public function testLoadWithEntityIdResolver()
    {
        $ext       = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load(['iltar_http' => ['router' => ['entity_id_resolver' => true]]], $container);

        $this->assertTrue($container->hasParameter('iltar.http.router.enabled'));
        $this->assertTrue($container->hasParameter('iltar.http.router.entity_id_resolver.enabled'));
        $this->assertTrue($container->has('iltar.http.router.entity_id_resolver'));
    }

    public function testLoadNoRouter()
    {
        $ext       = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load(['iltar_http' => ['router' => ['enabled' => false]]], $container);

        $this->assertFalse($container->has('iltar.http.router.parameter_resolver_collection'));
        $this->assertFalse($container->hasParameter('iltar.http.router.enabled'));
    }
}
