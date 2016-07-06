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
            $reflection = new \ReflectionClass($context);
            $contexts[] = [
                'short' => $reflection->getShortName(),
                'full' => trim($context)
            ];
        }
        $templateFile = __DIR__ . '/../resources/Container.tpl.php';
        $code = $this->renderTemplate(['contexts' => $contexts], $templateFile);
        $codeFile = $this->contextsPath . "/Container.php";
        file_put_contents($codeFile, $code);
        $this->containerHasBeenBuilt = true;
    }

    private function renderTemplate($variables, $templateFile)
    {
        extract($variables);
        ob_start();
        require $templateFile;
        return ob_get_clean();
    }
}