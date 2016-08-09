<?php

namespace GForces\Behat\ContextAutoloaderExtension\Code;

class Builder
{
    private $contextsPath;

    private $containerHasBeenBuilt = false;

    /**
     * @param mixed $contextsPath
     */
    public function setContextsPath($contextsPath)
    {
        $this->contextsPath = $contextsPath;
    }

    public function buildContextContainer($contextsForLinking)
    {
        if ($this->containerHasBeenBuilt) {
            return;
        }
        $contexts = [];
        foreach ($contextsForLinking as $context) {
            $varName = $this->getContextVarName($context, $contexts);
            $contexts[$varName] = [
                'short' => $varName,
                'full' => trim($context)
            ];
        }
        $templateFile = __DIR__ . '/../resources/Container.tpl.php';
        $code = $this->renderTemplate(['contexts' => $contexts], $templateFile);
        $codeFile = $this->contextsPath . "/Container.php";
        file_put_contents($codeFile, $code);
        $this->containerHasBeenBuilt = true;
    }

    private function getContextVarName($context, array $contexts)
    {
        $reflection = new \ReflectionClass($context);
        $name = $reflection->getShortName();
        return isset($contexts[$name]) ? basename(str_replace('\\', DIRECTORY_SEPARATOR, $reflection->getNamespaceName())) . $name : $name;
    }

    private function renderTemplate($variables, $templateFile)
    {
        extract($variables);
        ob_start();
        require $templateFile;
        return ob_get_clean();
    }
}