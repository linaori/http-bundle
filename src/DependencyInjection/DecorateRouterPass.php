<?php
namespace Iltar\HttpBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
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
        if (!$container->hasParameter('iltar.http.router.enabled')) {
            return;
        }

        $resolvers = [];

        foreach (array_keys($container->findTaggedServiceIds('router.parameter_resolver')) as $serviceId) {
            $newId = 'iltar.http.parameter_resolver.' . $serviceId;

            $container->setDefinition(
                $newId,
                (new DefinitionDecorator('iltar.http.parameter_resolver.abstract'))->replaceArgument(1, $serviceId)
            );

            $resolvers[] = new Reference($newId);
        }

        if (empty($resolvers)) {
            return;
        }

        $container->setDefinition(
            'iltar.http.parameter_resolving_router',
            (new DefinitionDecorator('iltar.http.parameter_resolving_router.abstract'))->setDecoratedService('router')
        );

        $container->findDefinition('iltar.http.router.parameter_resolver_collection')->replaceArgument(0, $resolvers);
    }
}
