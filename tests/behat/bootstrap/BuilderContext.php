<?php

namespace hiqdev\php\billing\tests\behat\bootstrap;

use Behat\Behat\Context\Context;

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
