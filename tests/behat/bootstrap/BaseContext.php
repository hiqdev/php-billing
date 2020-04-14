<?php

namespace hiqdev\php\billing\tests\behat\bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

abstract class BaseContext implements Context
{
    protected $builder;

    /**
     * @BeforeScenario
     */
    public function getBuilder(BeforeScenarioScope $scope)
    {
        $this->builder = $scope->getEnvironment()->getContext(BuilderContext::class)->getBuilder();
    }
}
