<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
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

    protected function setUp(): void
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
        $modifier = $this->testAddons($this->modifier);
        $discount = $modifier->discount();
        $this->assertReason($discount);
        $this->assertSince($discount);
        $this->assertTill($discount);
    }

    public function testAddons()
    {
        $modifier = $this->checkReason($this->modifier);
        $modifier = $this->checkSince($modifier);
        $modifier = $this->checkTill($modifier);

        return $modifier;
    }

    public function testReason()
    {
        $this->checkReason($this->modifier);
    }

    public function checkReason(Modifier $modifier)
    {
        $result = $modifier->reason(self::SOME_TEXT);
        $this->assertReason($result);

        return $result;
    }

    public function assertReason(Modifier $modifier)
    {
        $reason = $modifier->getReason();
        $this->assertInstanceOf(Reason::class, $reason);
        $this->assertSame(self::SOME_TEXT, $reason->getValue());
    }

    public function testSince()
    {
        $this->checkSince($this->modifier);
    }

    public function checkSince(Modifier $modifier)
    {
        $result = $modifier->since($this->now);
        $this->assertSince($result);

        return $result;
    }

    public function assertSince(Modifier $modifier)
    {
        $since = $modifier->getSince();
        $this->assertInstanceOf(Since::class, $since);
        $this->assertSame($this->now, $since->getValue());
    }

    public function testTill()
    {
        $this->checkTill($this->modifier);
    }

    public function checkTill(Modifier $modifier)
    {
        $result = $modifier->till($this->now);
        $this->assertTill($result);

        return $result;
    }

    public function assertTill(Modifier $modifier)
    {
        $till = $modifier->getTill();
        $this->assertInstanceOf(Till::class, $till);
        $this->assertSame($this->now, $till->getValue());
    }
}
