<?php
namespace Iltar\HttpBundle\Router;

/**
 * Works as a reversed ParameterConverter from the SensioFrameworkExtraBundle.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
interface ParameterResolverInterface
{
    /**
     * Check if the parameter is supported.
     *
     * @param string $name   Name of the parameter as defined in the Route
     * @param mixed  $value  Value of the parameter as filled in
     * @return bool
     */
    public function supportsParameter($name, $value);

    /**
     * Resolves a parameter to a string.
     *
     * Example:
     *  - Route: "/profile/{username}/"
     *  - generate("app.profile.view", ["username" => $user]
     *  - { return $user->getUsername(); }
     *
     * @param string $name   Name of the parameter as defined in the Route
     * @param mixed  $value  Value of the parameter as filled in
     * @return string|null   Return null if nothing could be resolved
     */
    public function resolveParameter($name, $value);
}
