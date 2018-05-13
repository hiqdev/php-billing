<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\charge\modifiers\addons\Reason;
use hiqdev\php\billing\charge\modifiers\addons\Since;
use hiqdev\php\billing\charge\modifiers\addons\Till;
use hiqdev\php\billing\charge\modifiers\Discount;
use hiqdev\php\billing\charge\modifiers\Modifier;

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

    public function testDiscount()
    {
        $discount = $this->modifier->discount();
        $this->assertTrue($discount !== $this->modifier);
        $this->assertInstanceOf(Discount::class, $discount);
    }

    public function testAddonsCopied()
    {
        $this->testAddons();
        $discount = $this->modifier->discount();
        $this->assertReason($discount);
        $this->assertSince($discount);
        $this->assertTill($discount);
    }

    public function testAddons()
    {
        $this->checkReason();
        $this->checkSince();
        $this->checkTill();
    }

    public function testReason()
    {
        $this->checkReason();
    }

    public function checkReason()
    {
        $result = $this->modifier->reason(self::SOME_TEXT);
        $this->assertSame($this->modifier, $result);
        $this->assertReason($this->modifier);
    }

    public function assertReason($modifier)
    {
        $reason = $modifier->getReason();
        $this->assertInstanceOf(Reason::class, $reason);
        $this->assertSame(self::SOME_TEXT, $reason->getValue());
    }

    public function testSince()
    {
        $this->checkSince();
    }

    public function checkSince()
    {
        $result = $this->modifier->since($this->now);
        $this->assertSame($this->modifier, $result);
        $this->assertSince($this->modifier);
    }

    public function assertSince(Modifier $modifier)
    {
        $since = $modifier->getSince();
        $this->assertInstanceOf(Since::class, $since);
        $this->assertSame($this->now, $since->getValue());
    }

    public function testTill()
    {
        $this->checkTill();
    }

    public function checkTill()
    {
        $result = $this->modifier->till($this->now);
        $this->assertSame($this->modifier, $result);
        $this->assertTill($this->modifier);
    }

    public function assertTill(Modifier $modifier)
    {
        $till = $modifier->getTill();
        $this->assertInstanceOf(Till::class, $till);
        $this->assertSame($this->now, $till->getValue());
    }
}
