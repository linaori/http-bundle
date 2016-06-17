<?php

namespace Iltar\HttpBundle\DependencyInjection;

use Iltar\HttpBundle\DoctrineBridge\Router\EntityIdResolver;
use Iltar\HttpBundle\Router\MappablePropertyPathResolver;
use Iltar\HttpBundle\Router\ParameterResolverInterface;
use Iltar\HttpBundle\Router\ParameterResolvingRouter;
use Iltar\HttpBundle\Router\ResolverCollection;
use Iltar\HttpBundle\Router\ResolverCollectionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class IltarHttpExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->processRouterConfig($config['router'], $container, $loader);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param XmlFileLoader    $loader
     */
    private function processRouterConfig(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        if (!$this->isConfigEnabled($container, $config)) {
            return;
        }

        $loader->load('router.xml');
        $mapping = $this->convertMappedGetters($config['mapped_properties']);
        $container->getDefinition('iltar_http.router.mapped_properties')->replaceArgument(1, $mapping);

        $this->addClassesToCompile([
            MappablePropertyPathResolver::class,
            ParameterResolverInterface::class,
            ParameterResolvingRouter::class,
            ResolverCollectionInterface::class,
            ResolverCollection::class,
        ]);

        if (false !== $config['entity_id_resolver']) {
            $loader->load('entity_id_resolver.xml');
            $this->addClassesToCompile([EntityIdResolver::class]);
        }
    }

    /**
     * @param array $mapping
     *
     * @return array
     */
    private function convertMappedGetters(array $mapping)
    {
        $mapped = [];
        foreach ($mapping as $path => $property) {
            $exploded = explode('.', $path, 2);

            $key = '_fallback';

            if (count($exploded) === 2) {
                $key = $exploded[1];
            }

            // `App\AppUser.username: ~` would become `App\AppUser.username: username`
            if (!is_string($property) || '~' === $property) {
                $property = $key;
            }

            $mapped[$exploded[0]][$key] = $property;
        }

        return $mapped;
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://iltar.github.io/schema/dic/http-bundle';
    }
}
