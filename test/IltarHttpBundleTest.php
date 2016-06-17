<?php

namespace Iltar\HttpBundle;

use Iltar\HttpBundle\DependencyInjection\DecorateRouterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
class IltarHttpBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $container = new ContainerBuilder();
        $bundle = new IltarHttpBundle();

        $bundle->build($container);

        $passes = $container->getCompiler()->getPassConfig()->getBeforeOptimizationPasses();
        self::assertInstanceOf(DecorateRouterPass::class, $passes[0]);
    }
}
