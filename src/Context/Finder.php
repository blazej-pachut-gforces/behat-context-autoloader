<?php

namespace GForces\Behat\ContextAutoloaderExtension\Context;

use GForces\Behat\ContextAutoloaderExtension\Code\Parser;

class Finder
{
    /** @var \Symfony\Component\Finder\Finder */
    private $finder;

    /** @var Parser */
    private $parser;

    /** @var String */
    private $contextsPath;

    private $cachedResult;

    /**
     * Finder constructor.
     * @param \Symfony\Component\Finder\Finder $finder
     * @param Parser $parser
     */
    public function __construct(\Symfony\Component\Finder\Finder $finder, Parser $parser)
    {
        $this->finder = $finder;
        $this->parser = $parser;
    }

    /**
     * @param String $contextPaths
     */
    public function setContextsPath($contextsPaths)
    {
        $this->contextsPath = $contextsPaths;
    }

    public function getContexts()
    {
        if ($this->cachedResult) {
            return $this->cachedResult;
        }
        $result = [];
        $this->finder
            ->in($this->contextsPath)
            ->notContains('abstract class')
            ->notContains('class BaseContext')
            ->notContains('class Container')
            ->notContains('/trait .*/')
            ->name('*.php');
        foreach ($this->finder as $foundContextFile) {
            $source = file_get_contents($foundContextFile);
            if ($foundClass = $this->parser->getClassFromSource($source)) {
                $result[] = $foundClass;
            }
        }
        return $this->cachedResult = $result;
    }

}