<?php

namespace Iltar\HttpBundle\DependencyInjection;

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
        if (!$container->hasParameter('iltar_http.router.enabled')) {
            return;
        }

        $resolvers = [];

        foreach ($container->findTaggedServiceIds('router.parameter_resolver') as $serviceId => $tags) {
            $tag = current($tags);
            if (!array_key_exists('priority', $tag)) {
                throw new \InvalidArgumentException(
                    sprintf('The router.parameter_resolver tag requires a priority to be set for %s.', $serviceId)
                );
            }

            $resolvers[] = [
                'priority' => $tag['priority'],
                'service' => $serviceId,
            ];
        }

        usort($resolvers, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return strcmp($a['service'], $b['service']);
            }

            return $a['priority'] > $b['priority'] ? -1 : 1;
        });

        $container
            ->findDefinition('iltar_http.router.parameter_resolver_collection')
            ->replaceArgument(0, array_map(function (array $resolver) {
                return new Reference($resolver['service']);
            }, $resolvers));
    }
}
