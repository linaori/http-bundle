<?php
namespace Iltar\HttpBundle\Router;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class ParameterResolvingRouter implements RouterInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        return $this->router->setContext($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->router->getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        return $this->router->getRouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->router->generate($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        return $this->router->match($pathinfo);
    }
}
