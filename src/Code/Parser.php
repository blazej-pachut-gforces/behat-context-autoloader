<?php

namespace GForces\Behat\ContextAutoloaderExtension\Code;

class Parser
{
    private function getClassName($source)
    {
        $classDefinitionRegexp = '~(abstract)?\sclass\s(\w*?)?\s(?:extends)?\s?(\w*)?~';
        return $this->getRegexpResult($classDefinitionRegexp, $source, 2);
    }

    private function getNamespace($source)
    {
        $namespaceRegexp = '~namespace ([^;]*)~';
        return $this->getRegexpResult($namespaceRegexp, $source, 1);
    }

    public function getClassFromSource($source)
    {
        $class = $this->getClassName($source);
        if (!$class) {
            return false;
        }
        $namespace = $this->getNamespace($source);
        $fullClassNameTokens = [];
        if ($namespace) {
            $fullClassNameTokens[] = $namespace;
        }
        $fullClassNameTokens[] = $class;
        return join("\\", $fullClassNameTokens);
    }

    private function getRegexpResult($regexp, $subject, $resultIndex, $notFoundValue = false)
    {
        $matches = [];
        preg_match($regexp, $subject, $matches);
        if (!isset($matches[$resultIndex])) {
            return $notFoundValue;
        }
        return $matches[$resultIndex];
    }

}