<?php
namespace Iltar\HttpBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
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
    }

    public function testLoadNoRouter()
    {
        $ext       = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load(['iltar_http' => ['router' => false]], $container);

        $this->assertFalse($container->has('iltar.http.router.parameter_resolver_collection'));
    }
}
