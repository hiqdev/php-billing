<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\behat\bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

abstract class BaseContext implements Context
{
    /** @var BuilderInterface */
    protected $builder;

    /**
     * @BeforeScenario
     */
    public function getBuilder(BeforeScenarioScope $scope)
    {
        $this->builder = $scope->getEnvironment()->getContext(BuilderContext::class)->getBuilder();
    }
}
