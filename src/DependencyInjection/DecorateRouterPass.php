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

        foreach ($container->findTaggedServiceIds('router.parameter_resolver') as $serviceId => $tags) {
            $tag = current($tags);
            if (!array_key_exists('priority', $tag)) {
                throw new \InvalidArgumentException(
                    'The router.parameter_resolver tag requires a priority to be set for ' . $serviceId . '.'
                );
            }
            $newId = 'iltar.http.parameter_resolver.' . $serviceId;

            $container->setDefinition(
                $newId,
                (new DefinitionDecorator('iltar.http.parameter_resolver.abstract'))->replaceArgument(1, $serviceId)
            );

            $resolvers[] = [
                'priority' => $tag['priority'],
                'service'  => $newId
            ];
        }

        if (empty($resolvers)) {
            return;
        }

        $container->setDefinition(
            'iltar.http.parameter_resolving_router',
            (new DefinitionDecorator('iltar.http.parameter_resolving_router.abstract'))->setDecoratedService('router')
        );

        usort($resolvers, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return 0;
            }

            return $a['priority'] > $b['priority'] ? -1 : 1;
        });

        $container->findDefinition('iltar.http.router.parameter_resolver_collection')
            ->replaceArgument(0, array_map(function (array $resolver) {
                return new Reference($resolver['service']);
            }, $resolvers));
    }
}
