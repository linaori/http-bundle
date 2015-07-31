<?php
namespace Iltar\HttpBundle;

use Iltar\HttpBundle\DependencyInjection\DecorateRouterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\IltarHttpBundle
 */
class IltarHttpBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $container = new ContainerBuilder();
        $bundle    = new IltarHttpBundle();

        $bundle->build($container);

        $passes = $container->getCompiler()->getPassConfig()->getBeforeOptimizationPasses();
        $this->assertInstanceOf(DecorateRouterPass::class, $passes[0]);
    }
}
