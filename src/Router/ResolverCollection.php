<?php
namespace Iltar\HttpBundle\Router;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class ResolverCollection implements ResolverCollectionInterface
{
    /**
     * @var ParameterResolverInterface[]
     */
    private $resolvers;

    /**
     * @param ParameterResolverInterface[] $resolvers
     */
    public function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * Checks all resolvers until a matching resolver is found.
     *
     * {@inheritdoc}
     */
    public function resolve(array $parameters)
    {
        $resolved = [];

        foreach ($parameters as $key => $value) {
            // no need to resolve scalars to another value
            $resolved[$key] = is_scalar($value) ? $value : null;

            foreach ($this->resolvers as $resolver) {
                if (null !== $resolved[$key]) {
                    break;
                }

                if ($resolver->supportsParameter($key, $value)) {
                    $resolved[$key] = $resolver->resolveParameter($key, $value);
                }
            }
        }

        return $resolved;
    }
}
