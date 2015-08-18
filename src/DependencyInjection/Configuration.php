<?php
namespace Iltar\HttpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('iltar_http');

        $this->addRouterConfig($rootNode);

        return $treeBuilder;
    }

    /**
     * Resolves iltar_http.router
     *
     * @param ArrayNodeDefinition $rootNode
     */
    private function addRouterConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('router')
                    ->info('Configures the ParameterResolvingRouter')
                    ->canBeDisabled()
                    ->fixXmlConfig('mapped_property')
                    ->children()
                        ->booleanNode('entity_id_resolver')
                            ->info('Enable or disable the EntityIdResolver')
                            ->defaultFalse()
                        ->end() // entity_id_resolver
                        ->arrayNode('mapped_properties')
                            ->info('Example: `App\User.username: username`')
                            ->requiresAtLeastOneElement()
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end() // mapped_properties
                    ->end()
                ->end() // router
            ->end();
    }
}
