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

        $this->addRouteConfig($rootNode);

        return $treeBuilder;
    }

    /**
     * Resolves iltar_http.router
     *
     * @param ArrayNodeDefinition $rootNode
     */
    private function addRouteConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('router')->defaultTrue()->end()
            ->end();
    }
}
