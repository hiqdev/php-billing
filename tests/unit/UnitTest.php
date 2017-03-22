<?php

namespace hiqdev\php\billing\tests;

use hiqdev\php\billing\Unit;

/**
 * Unit testing class.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class UnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Unit
     */
    protected $byte;

    /**
     * @var Unit
     */
    protected $mb;

    protected function setUp()
    {
        $this->byte = Unit::Byte();
    }

    protected function tearDown()
    {
    }
}
