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
        $ext       = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load([], $container);

        $this->assertTrue($container->has('iltar_http.router.parameter_resolver_collection'));
        $this->assertTrue($container->hasParameter('iltar_http.router.enabled'));
        $this->assertFalse($container->hasParameter('iltar_http.router.entity_id_resolver.enabled'));
    }

    public function testLoadMappedResolvers()
    {
        $ext       = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load([
            'iltar_http' => [
                'router' => [
                    'mapped_getters' => [
                        'App\Post'          => 'slug',
                        'App\User'          => 'id',
                        'App\User.username' => 'username',
                        'App\Reply.id'      => 'id',
                        'App\Message.re.id' => 'id',
                    ]
                ]
            ]
        ], $container);

        $expected = [
            'App\Post' => [
                '_fallback' => 'slug',
            ],
            'App\User' => [
                '_fallback' => 'id',
                'username'  => 'username',
            ],
            'App\Reply' => [
                'id' => 'id',
            ],
            'App\Message' => [
                're.id' => 'id',
            ],
        ];

        $this->assertSame($expected, $container->getDefinition('iltar_http.router.mapped_getters')->getArgument(1));
    }

    public function testLoadWithEntityIdResolver()
    {
        $ext       = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load(['iltar_http' => ['router' => ['entity_id_resolver' => true]]], $container);

        $this->assertTrue($container->hasParameter('iltar_http.router.enabled'));
        $this->assertTrue($container->hasParameter('iltar_http.router.entity_id_resolver.enabled'));
        $this->assertTrue($container->has('iltar_http.router.entity_id_resolver'));
    }

    public function testLoadNoRouter()
    {
        $ext       = new IltarHttpExtension();
        $container = new ContainerBuilder();
        $ext->load(['iltar_http' => ['router' => ['enabled' => false]]], $container);

        $this->assertFalse($container->has('iltar_http.router.parameter_resolver_collection'));
        $this->assertFalse($container->hasParameter('iltar_http.router.enabled'));
    }
}
