<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\FixedDiscountTest;

use hiqdev\php\billing\charge\modifiers\Modifier;
use hiqdev\php\billing\charge\modifiers\addons\Reason;
use hiqdev\php\billing\charge\modifiers\addons\Since;
use hiqdev\php\billing\charge\modifiers\addons\Till;
use DateTimeImmutable;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ModifierTest extends \PHPUnit\Framework\TestCase
{
    protected $modifier;

    const SOME_TEXT = 'some text';

    protected function setUp()
    {
        parent::setUp();
        $this->now = new DateTimeImmutable();
        $this->modifier = new Modifier();
    }

    public function testAddons()
    {
        $this->modifier->reason(self::SOME_TEXT);
        $this->modifier->since($this->now);
        $this->modifier->till($this->now);
        $reason = $this->modifier->getReason();
        $since = $this->modifier->getSince();
        $till = $this->modifier->getTill();
        $this->assertInstanceOf(Reason::class, $reason);
        $this->assertInstanceOf(Since::class, $since);
        $this->assertInstanceOf(Till::class, $till);
        $this->assertSame(self::SOME_TEXT, $reason->getValue());
        $this->assertSame($this->now, $since->getValue());
        $this->assertSame($this->now, $till->getValue());
    }
}
