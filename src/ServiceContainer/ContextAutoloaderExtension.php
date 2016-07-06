<?php

namespace GForces\Behat\ContextAutoloaderExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\Environment\ServiceContainer\EnvironmentExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;

use GForces\Behat\ContextAutoloaderExtension\Code\Builder;
use GForces\Behat\ContextAutoloaderExtension\Code\Parser;

use GForces\Behat\ContextAutoloaderExtension\Context\Finder;
use GForces\Behat\ContextAutoloaderExtension\Environment\Handler as EnvironmentHandler;
use GForces\Behat\ContextAutoloaderExtension\Context\Handler as ContextHandler;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ContextAutoloaderExtension implements Extension
{

    const ENVIRONMENT_HANDLER_ID = 'gforces.contextautoloader.environment.initializer';
    const CONTEXT_HANDLER_ID = 'gforces.contextautoloader.context.initializer';
    const SYMFONY_FINDER_ID = 'gforces.contextautoloader.symfony.finder';
    const CONTEXT_FINDER_ID = 'gforces.contextautoloader.context.finder';
    const CODE_PARSER_ID = 'gforces.contextautoloader.code.parser';
    const BUILDER_ID = 'gforces.contextautoloader.code.builder';
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // TODO: Implement process() method.
    }

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'buarzej';
        // TODO: Implement getConfigKey() method.
    }

    /**
     * Initializes other extensions.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows extensions
     * to hook into the configuration of other extensions providing such an
     * extension point.
     *
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        // TODO: Implement initialize() method.
    }

    /**
     * Setups configuration for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder->children()
            ->scalarNode('contexts_path')
            ->defaultNull()
        ->end();
    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->registerContextFinder($container, $config);
        $this->registerContextBaseClassBuild($container, $config);
        $this->registerEnvironmentInitializerHookService($container);
        $this->registerContextInitializerHookService($container);
    }

    private function registerEnvironmentInitializerHookService(ContainerBuilder $container)
    {
        $definition = new Definition(EnvironmentHandler::class, [
            new Reference(ContextExtension::FACTORY_ID),
            new Reference(EnvironmentExtension::HANDLER_TAG . '.context'),
            new Reference(self::CONTEXT_FINDER_ID),
            new Reference(self::BUILDER_ID),
            new Reference(self::CONTEXT_HANDLER_ID)
        ]);
        $definition->addTag(EnvironmentExtension::HANDLER_TAG, ['priority' => 100]);
        $container->setDefinition(self::ENVIRONMENT_HANDLER_ID, $definition);
    }

    private function registerContextInitializerHookService(ContainerBuilder $container)
    {
        $definition = new Definition(ContextHandler::class);
        $definition->addTag(ContextExtension::INITIALIZER_TAG);
        $container->setDefinition(self::CONTEXT_HANDLER_ID, $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function registerContextFinder($container, $config)
    {
        $symfonyFinderDefinition = new Definition(\Symfony\Component\Finder\Finder::class);
        $symfonyFinderDefinition->setShared(false);
        $container->setDefinition(self::SYMFONY_FINDER_ID, $symfonyFinderDefinition);
        $container->register(self::CODE_PARSER_ID, Parser::class);
        $finderDefinition = new Definition(Finder::class, [
            new Reference(self::SYMFONY_FINDER_ID),
            new Reference(self::CODE_PARSER_ID)
        ]);
        $finderDefinition->addMethodCall('setContextsPath', [$config['contexts_path']]);
        $container->setDefinition(self::CONTEXT_FINDER_ID, $finderDefinition);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function registerContextBaseClassBuild($container, $config)
    {
        $builderDefinition = new Definition(Builder::class);
        $builderDefinition->addMethodCall('setContextsPath', [$config['contexts_path']]);
        $container->setDefinition(self::BUILDER_ID, $builderDefinition);
    }
}