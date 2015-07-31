<?php
namespace Iltar\HttpBundle\DependencyInjection;

use Iltar\HttpBundle\Router\ParameterResolvingRouter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class DecorateRouterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container
            ->register('iltar.http.parameter_resolving_router')
            ->setClass(ParameterResolvingRouter::class)
            ->addArgument(new Reference('iltar.http.parameter_resolving_router.inner'))
            ->setPublic(false)
            ->setDecoratedService('router');
    }
}
