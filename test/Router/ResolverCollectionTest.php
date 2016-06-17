<?php

namespace Iltar\HttpBundle\Router;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\Router\ResolverCollection
 */
class ResolverCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider resolveProvider
     */
    public function testResolve(array $input, array $expected, CallableResolver $resolver)
    {
        self::assertEquals($expected, (new ResolverCollection([$resolver]))->resolve($input));
    }

    public function resolveProvider()
    {
        return [
            [
                ['user' => 'henk', 'profile' => 42],
                ['user' => 'henk', 'profile' => 42],
                new CallableResolver(function ($key) {
                    return $key === 'user';
                }, function ($key, $value) {
                    return $value.'-user';
                }),
            ],
            [
                ['user' => new \stdClass()],
                ['user' => 'henk'],
                new CallableResolver(function ($key) {
                    return $key === 'user';
                }, function () {
                    return 'henk';
                }),
            ],
            [
                ['user' => new \stdClass()],
                ['user' => new \stdClass()],
                new CallableResolver(function () {
                    return false;
                }, function () { }),
            ],
        ];
    }
}

class CallableResolver implements ParameterResolverInterface
{
    private $supports;
    private $resolves;

    public function __construct(callable $supports, callable $resolves)
    {
        $this->supports = $supports;
        $this->resolves = $resolves;
    }

    public function supportsParameter($name, $value)
    {
        $m = $this->supports;

        return $m($name, $value);
    }

    public function resolveParameter($name, $value)
    {
        $m = $this->resolves;

        return $m($name, $value);
    }
}
