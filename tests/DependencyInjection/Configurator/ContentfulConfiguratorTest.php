<?php

namespace Flagception\Tests\FlagceptionBundle\DependencyInjection\Configurator;

use Flagception\Bundle\FlagceptionBundle\DependencyInjection\Configurator\ContentfulConfigurator;
use Flagception\Bundle\FlagceptionBundle\DependencyInjection\FlagceptionExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ContentfulConfiguratorTest
 *
 * @author Michel Chowanski <chowanski@bestit-online.de>
 * @package Flagception\Tests\FlagceptionBundle\DependencyInjection\Configurator
 */
class ContentfulConfiguratorTest extends TestCase
{
    /**
     * The container
     *
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $container = new ContainerBuilder();
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../../src/Resources/config'));
        $loader->load('configurators.yml');

        $this->container = $container;
    }

    /**
     * Test key
     *
     * @return void
     */
    public function testKey()
    {
        static::assertEquals('contentful', (new ContentfulConfigurator())->getKey());
    }

    /**
     * Test activator default state
     *
     * @return void
     */
    public function testActivatorDefaultState()
    {
        $config = [];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertFalse($this->container->hasDefinition('flagception.activator.contentful_activator'));
    }

    /**
     * Test activator default state
     *
     * @return void
     */
    public function testActivatorDefaultPriority()
    {
        $config = [
            [
                'activators' => [
                    'contentful' => [
                        'enable' => true,
                        'client_id' => 'foobar'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        $definition = $this->container->getDefinition('flagception.activator.contentful_activator');
        static::assertEquals(150, $definition->getTag('flagception.activator')[0]['priority']);
    }

    /**
     * Test activator can be disabled
     *
     * @return void
     */
    public function testActivatorCanByDisabled()
    {
        $config = [
            [
                'activators' => [
                    'contentful' => [
                        'enable' => false,
                        'client_id' => 'foobar'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertFalse($this->container->hasDefinition('flagception.activator.contentful_activator'));
    }

    /**
     * Test activator can be disabled by string
     *
     * @return void
     */
    public function testActivatorCanByDisabledByString()
    {
        $config = [
            [
                'activators' => [
                    'contentful' => [
                        'enable' => 'false',
                        'client_id' => 'foobar'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertFalse($this->container->hasDefinition('flagception.activator.contentful_activator'));
    }

    /**
     * Test activator can be enabled
     *
     * @return void
     */
    public function testActivatorCanByEnabled()
    {
        $config = [
            [
                'activators' => [
                    'contentful' => [
                        'enable' => true,
                        'client_id' => 'foobar'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertTrue($this->container->hasDefinition('flagception.activator.contentful_activator'));
    }

    /**
     * Test activator can be enabled by string
     *
     * @return void
     */
    public function testActivatorCanByEnabledByString()
    {
        $config = [
            [
                'activators' => [
                    'contentful' => [
                        'enable' => 'true',
                        'client_id' => 'foobar'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertTrue($this->container->hasDefinition('flagception.activator.contentful_activator'));
    }

    /**
     * Test set activator priority
     *
     * @return void
     */
    public function testActivatorSetPriority()
    {
        $config = [
            [
                'activators' => [
                    'contentful' => [
                        'enable' => true,
                        'priority' => 10,
                        'client_id' => 'foobar'
                    ]
                ]
            ]
        ];
        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        $definition = $this->container->getDefinition('flagception.activator.contentful_activator');
        static::assertEquals(10, $definition->getTag('flagception.activator')[0]['priority']);
    }

    /**
     * Test minimal configuration
     *
     * @return void
     */
    public function testMinimalConfiguration()
    {
        $config = [
            [
                'activators' => [
                    'contentful' => [
                        'enable' => true,
                        'client_id' => 'contentful_service_id'
                    ]
                ]
            ]
        ];

        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertEquals(
            new Reference('contentful_service_id'),
            $this->container->getDefinition('flagception.activator.contentful_activator')->getArgument(0)
        );

        static::assertEquals(
            'flagception',
            $this->container->getDefinition('flagception.activator.contentful_activator')->getArgument(1)
        );

        static::assertEquals(
            [
                'name' => 'name',
                'state' => 'state'
            ],
            $this->container->getDefinition('flagception.activator.contentful_activator')->getArgument(2)
        );
    }

    /**
     * Test full configuration
     *
     * @return void
     */
    public function testFullConfiguration()
    {
        $config = [
            [
                'activators' => [
                    'contentful' => [
                        'enable' => true,
                        'client_id' => 'contentful_service_id',
                        'content_type' => $contentType = uniqid(),
                        'mapping' => [
                            'name' => $name = uniqid(),
                            'state' => $state = uniqid()
                        ]
                    ]
                ]
            ]
        ];

        $extension = new FlagceptionExtension();
        $extension->load($config, $this->container);

        static::assertEquals(
            new Reference('contentful_service_id'),
            $this->container->getDefinition('flagception.activator.contentful_activator')->getArgument(0)
        );

        static::assertEquals(
            $contentType,
            $this->container->getDefinition('flagception.activator.contentful_activator')->getArgument(1)
        );

        static::assertEquals(
            [
                'name' => $name,
                'state' => $state
            ],
            $this->container->getDefinition('flagception.activator.contentful_activator')->getArgument(2)
        );
    }
}
