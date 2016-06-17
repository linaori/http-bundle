<?php

namespace Iltar\HttpBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <kjarli@gmail.com>
 * @covers Iltar\HttpBundle\DependencyInjection\Configuration
 * @covers Iltar\HttpBundle\DependencyInjection\IltarHttpExtension
 */
class IltarHttpExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadWithRouter()
    {
        $ext = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load([], $container);

        self::assertTrue($container->has('iltar_http.router.parameter_resolver_collection'));
        self::assertTrue($container->hasParameter('iltar_http.router.enabled'));
        self::assertFalse($container->hasParameter('iltar_http.router.entity_id_resolver.enabled'));
    }

    public function testLoadMappedResolvers()
    {
        $ext = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load([
            'iltar_http' => [
                'router' => [
                    'mapped_properties' => [
                        'App\Post' => 'slug',
                        'App\User' => 'id',
                        'App\User.user' => 'username',
                        'App\User.username' => '~',
                        'App\Reply.rid' => 'id',
                        'App\Reply.id' => null,
                        'App\Message.re.id' => 'id',
                    ],
                ],
            ],
        ], $container);

        $expected = [
            'App\Post' => [
                '_fallback' => 'slug',
            ],
            'App\User' => [
                '_fallback' => 'id',
                'user' => 'username',
                'username' => 'username',
            ],
            'App\Reply' => [
                'rid' => 'id',
                'id' => 'id',
            ],
            'App\Message' => [
                're.id' => 'id',
            ],
        ];

        self::assertSame($expected, $container->getDefinition('iltar_http.router.mapped_properties')->getArgument(1));
    }

    public function testLoadWithEntityIdResolver()
    {
        $ext = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load(['iltar_http' => ['router' => ['entity_id_resolver' => true]]], $container);

        self::assertTrue($container->hasParameter('iltar_http.router.enabled'));
        self::assertTrue($container->hasParameter('iltar_http.router.entity_id_resolver.enabled'));
        self::assertTrue($container->has('iltar_http.router.entity_id_resolver'));
    }

    public function testLoadNoRouter()
    {
        $ext = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load(['iltar_http' => ['router' => ['enabled' => false]]], $container);

        self::assertFalse($container->has('iltar_http.router.parameter_resolver_collection'));
        self::assertFalse($container->hasParameter('iltar_http.router.enabled'));
    }
}
