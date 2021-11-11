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
use Behat\Behat\Tester\Exception\PendingException;

class BuilderContext implements Context
{
    private $builder;

    public function __construct(string $class = null)
    {
        $class = $class ?? FactoryBasedBuilder::class;
        $this->builder = new $class();
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
