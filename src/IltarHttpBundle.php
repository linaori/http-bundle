<?php

namespace Iltar\HttpBundle;

use Iltar\HttpBundle\DependencyInjection\DecorateRouterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class IltarHttpBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DecorateRouterPass());
    }
}
