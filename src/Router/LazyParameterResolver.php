<?php
namespace Iltar\HttpBundle\Router;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Decorates the actual
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class LazyParameterResolver implements ParameterResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @param ContainerInterface $container
     * @param string             $serviceId
     */
    public function __construct(ContainerInterface $container, $serviceId)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsParameter($name, $value)
    {
        return $this->container->get($this->serviceId)->supportsParameter($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveParameter($name, $value)
    {
        return $this->container->get($this->serviceId)->resolveParameter($name, $value);
    }
}
