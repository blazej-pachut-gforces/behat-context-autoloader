<?php

namespace GForces\Behat\ContextAutoloaderExtension\Environment;

use Behat\Behat\Context\ContextFactory;
use Behat\Behat\Context\Environment\Handler\ContextEnvironmentHandler;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Environment\Handler\EnvironmentHandler;
use Behat\Testwork\Suite\Suite;
use GForces\Behat\ContextAutoloaderExtension\Code\Builder;
use GForces\Behat\ContextAutoloaderExtension\Context\Finder;

class Handler implements EnvironmentHandler
{
    /** @var ContextFactory */
    private $contextFactory;

    /** @var ContextEnvironmentHandler */
    private $contextEnvironmentHandler;

    /** @var Finder */
    private $contextFinder;

    /** @var Builder */
    private $contextBaseClassBuilder;

    /** @var \GForces\Behat\ContextAutoloaderExtension\Context\Handler */
    private $contextHandler;

    /**
     * Handler constructor.
     * @param ContextFactory $contextFactory
     * @param ContextEnvironmentHandler $contextEnvironmentHandler
     * @param Finder $contextFinder
     */
    public function __construct(ContextFactory $contextFactory, ContextEnvironmentHandler $contextEnvironmentHandler, Finder $contextFinder, Builder $contextBaseClassBuilder, \GForces\Behat\ContextAutoloaderExtension\Context\Handler $contextHandler) {
        $this->contextFactory = $contextFactory;
        $this->contextEnvironmentHandler = $contextEnvironmentHandler;
        $this->contextFinder = $contextFinder;
        $this->contextBaseClassBuilder = $contextBaseClassBuilder;
        $this->contextHandler = $contextHandler;
    }

    /**
     * Checks if handler supports provided suite.
     *
     * @param Suite $suite
     *
     * @return Boolean
     */
    public function supportsSuite(Suite $suite)
    {
        return false;
    }

    /**
     * Builds environment object based on provided suite.
     *
     * @param Suite $suite
     *
     * @return Environment
     */
    public function buildEnvironment(Suite $suite)
    {
        // Not used since supportsSuite returns false
    }

    /**
     * Checks if handler supports provided environment.
     *
     * @param Environment $environment
     * @param mixed $testSubject
     *
     * @return Boolean
     */
    public function supportsEnvironmentAndSubject(Environment $environment, $testSubject = null)
    {
        return $this->contextEnvironmentHandler->supportsEnvironmentAndSubject($environment, $testSubject);
    }

    /**
     * Isolates provided environment.
     *
     * @param Environment $environment
     * @param mixed $testSubject
     *
     * @return Environment
     */
    public function isolateEnvironment(Environment $environment, $testSubject = null)
    {
        $isolatedEnvironment = $this->contextEnvironmentHandler->isolateEnvironment($environment, $testSubject);
        $this->contextHandler->clearSharedContextData();
        $contexts = $this->contextFinder->getContexts();
        $this->contextBaseClassBuilder->buildContextContainer($contexts);
        foreach ($contexts as $contextClass) {
            $context = $this->contextFactory->createContext($contextClass);
            $isolatedEnvironment->registerContext($context);
        }
        return $isolatedEnvironment;
    }
}