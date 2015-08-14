<?php
namespace Iltar\HttpBundle\DependencyInjection;

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
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->processRouterConfig($config, $loader);
    }

    /**
     * @param array         $config
     * @param XmlFileLoader $loader
     */
    private function processRouterConfig(array $config, XmlFileLoader $loader)
    {
        if (false === $config['router']) {
            return;
        }

        $loader->load('router.xml');
    }
}
