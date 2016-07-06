<?php

namespace GForces\Behat\ContextAutoloaderExtension\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use behat\Context\WithSharedContextData;

class Handler implements ContextInitializer
{

    /** @var \SharedContextData */
    private $sharedContextData;

    public function clearSharedContextData()
    {
        $this->sharedContextData = new \SharedContextData();
    }

    /**
     * Initializes provided context.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof WithSharedContextData) {
            return;
        }
        $context->setSharedContextData($this->sharedContextData);
    }

}