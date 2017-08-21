<?php

namespace Iltar\HttpBundle\Exception;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
class UnresolvedParameterException extends \RuntimeException
{
    /**
     * @var string[]
     */
    private $parameters;

    /**
     * @param string   $route
     * @param string[] $parameters
     */
    public function __construct($route, array $parameters)
    {
        parent::__construct(sprintf(
            'Parameters for the route \'%s\' could not be resolved to scalar values. Parameters: "%s".',
            $route,
            implode(', ', array_keys($parameters))
        ));

        $this->parameters = $parameters;
    }

    /**
     * @return string[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
