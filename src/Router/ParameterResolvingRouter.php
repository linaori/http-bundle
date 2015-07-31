<?php
namespace Iltar\HttpBundle\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class ParameterResolvingRouter implements RouterInterface, RequestMatcherInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ResolverCollectionInterface
     */
    private $resolverCollection;

    /**
     * @param RouterInterface             $router
     * @param ResolverCollectionInterface $resolverCollection
     */
    public function __construct(RouterInterface $router, ResolverCollectionInterface $resolverCollection)
    {
        $this->router             = $router;
        $this->resolverCollection = $resolverCollection;
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
        return $this->router->generate($name, $this->resolverCollection->resolve($parameters), $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        return $this->router->match($pathinfo);
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        if (!$this->router instanceof RequestMatcherInterface) {
            throw new \BadMethodCallException('Router has to implement the ' . RequestMatcherInterface::class);
        }

        return $this->router->matchRequest($request);
    }
}
