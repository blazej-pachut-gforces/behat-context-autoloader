<?php
/**
 * @var array $contexts
 */
?>
<?= "<?php\n" ?>
/**
 * This is the Behat context container that allows easy cross-context access.
 * DO NOT MODIFY THIS FILE! It is automatically generated.
 */

namespace behat\Context;

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class Container
{
    /** @var \behat\Context\FeatureContext */
    public $featureContext;
    <?php foreach ($contexts as $context): ?>
/** @var \<?= $context['full'] ?> */
    public $<?= lcfirst($context['short']) ?>;
    <?php endforeach ?>

    public function initContexts(BeforeScenarioScope $scope)
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        $this->featureContext = $environment->getContext(\behat\Context\FeatureContext::class);
<?php foreach ($contexts as $context): ?>
        $this-><?= lcfirst($context['short']) ?> = $environment->getContext('<?= $context['full'] ?>');
<?php endforeach ?>    }
}
