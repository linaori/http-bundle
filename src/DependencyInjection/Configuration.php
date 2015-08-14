<?php
namespace Iltar\HttpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
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
                    ->canBeDisabled()
                    ->children()
                        ->booleanNode('entity_id_resolver')->defaultFalse()->end()
                    ->end()
            ->end();
    }
}
