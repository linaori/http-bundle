<?php

namespace Iltar\HttpBundle\Router;

use Iltar\HttpBundle\Router\Resolver\MappablePropertyPathResolver as BaseResolver;

@trigger_error(sprintf('%s is deprecated as of 1.1 and will be removed in 2.0. Use the %s instead.', MappablePropertyPathResolver::class, BaseResolver::class), E_USER_DEPRECATED);

/**
 * Resolves anything that's mapped.
 *
 * Arguments are passed via the constructor in an array: [
 *    'App\User' => [
 *      'username'   => 'username',
 *       '_fallback' => 'id',
 *    ,
 *    'App\Post' => ['_fallback' => 'slug'],
 *  ]
 *
 * Uses the Property Accessor to read properties given by importance:
 *   1. If mapped, the direct path
 *   2. If mapped, the _fallback
 *   3. If accessible, the Object.name
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 *
 * @deprecated as of 1.1 and will be removed in 2.0. Use the Iltar\HttpBundle\Router\Resolver\MappablePropertyPathResolver instead.
 */
class MappablePropertyPathResolver extends BaseResolver
{
}
