<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\type;

use hiqdev\php\billing\type\Type;
use hiqdev\php\billing\type\TypeInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class TypeTest extends \PHPUnit\Framework\TestCase
{
    protected $sid1 = 101;
    protected $sid2 = 102;

    protected $sop1 = 'server1';
    protected $sop2 = 'server2';

    protected $did1 = 201;
    protected $did2 = 202;

    protected $dop1 = 'domain1';
    protected $dop2 = 'domain2';

    protected function setUp()
    {
        $this->server11 = new Type($this->sid1,  $this->sop1);
        $this->server_1 = new Type(null,         $this->sop1);
        $this->server1_ = new Type($this->sid1,  null);
        $this->server22 = new Type($this->sid2,  $this->sop2);
        $this->server_2 = new Type(null,         $this->sop2);
        $this->server2_ = new Type($this->sid2,  null);

        $this->domain11 = new Type($this->did1,  $this->dop1);
        $this->domain_1 = new Type(null,         $this->dop1);
        $this->domain1_ = new Type($this->did1,  null);
        $this->domain22 = new Type($this->did2,  $this->dop2);
        $this->domain_2 = new Type(null,         $this->dop2);
        $this->domain2_ = new Type($this->did2,  null);
    }

    protected function tearDown()
    {
    }

    protected function copy(TypeInterface $type)
    {
        return new Type($type->getId(), $type->getName());
    }

    public function testGetUniqueId()
    {
        $this->assertSame($this->sid1,  $this->server11->getUniqueId());
        $this->assertSame($this->sop1,  $this->server_1->getUniqueId());
        $this->assertSame($this->sid1,  $this->server1_->getUniqueId());
        $this->assertSame($this->sid2,  $this->server22->getUniqueId());
        $this->assertSame($this->sop2,  $this->server_2->getUniqueId());
        $this->assertSame($this->sid2,  $this->server2_->getUniqueId());
    }

    public function testEquals()
    {
        $all = [$this->server11, $this->server_1, $this->server1_];
        $copies = [];
        foreach ($all as $k => $v) {
            $copies[$k] = $this->copy($v);
        }

        foreach ($all as $k => $v) {
            foreach ($copies as $j => $w) {
                $this->checkEquals($j === $k, $v, $w);
                $this->checkEquals($j === $k, $w, $v);
            }
        }
    }

    protected function checkEquals(bool $expect, $lhs, $rhs)
    {
        $check = $lhs->equals($rhs);
        if ($check !== $expect) {
            var_dump('no equality', $expect, $lhs, $rhs);
        }
        $this->assertSame($expect, $check);
    }

    public function testMatches()
    {
        $this->checkMatches([
            $this->server11, $this->copy($this->server11),
            $this->server_1, $this->copy($this->server_1),
        ]);
        $this->checkMatches([
            $this->server11, $this->copy($this->server11),
            $this->server1_, $this->copy($this->server1_),
        ]);
        $this->checkDoesntMatch([
            $this->server_1, $this->server1_, $this->server_2, $this->server2_,
            $this->domain_1, $this->domain1_, $this->domain_2, $this->domain2_,
        ]);
    }

    protected function checkDoesntMatch(array $types)
    {
        $this->checkMatches($types, false);
    }

    protected function checkMatches(array $types, bool $expect = true)
    {
        foreach ($types as $k => $v) {
            foreach ($types as $j => $w) {
                $this->checkSingleMatch($k === $j || $expect, $v, $w);
            }
        }
    }

    protected function checkSingleMatch(bool $expect, $lhs, $rhs)
    {
        $check = $lhs->matches($rhs);
        if ($check !== $expect) {
            var_dump('no match', $expect, $lhs, $rhs);
        }
        $this->assertSame($expect, $check);
    }
}
