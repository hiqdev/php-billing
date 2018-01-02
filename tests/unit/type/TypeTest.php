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
    protected $id1 = 1;

    protected $id2 = 2;

    protected $sr1 = 'server1';

    protected $sr2 = 'server2';

    protected function setUp()
    {
        $this->server11 = new Type($this->id1,  $this->sr1);
        $this->server_1 = new Type(null,        $this->sr1);
        $this->server1_ = new Type($this->id1,  null);
        $this->server22 = new Type($this->id2,  $this->sr2);
        $this->server_2 = new Type(null,        $this->sr2);
        $this->server2_ = new Type($this->id2,  null);

        $this->domain1 = new Type($this->id1, 'domain1');
        $this->domain2 = new Type($this->id2, 'domain2');
    }

    protected function tearDown()
    {
    }

    public function testGetUniqueId()
    {
        $this->assertSame($this->id1,   $this->server11->getUniqueId());
        $this->assertSame($this->sr1,   $this->server_1->getUniqueId());
        $this->assertSame($this->id1,   $this->server1_->getUniqueId());
        $this->assertSame($this->id2,   $this->server22->getUniqueId());
        $this->assertSame($this->sr2,   $this->server_2->getUniqueId());
        $this->assertSame($this->id2,   $this->server2_->getUniqueId());
    }

    public function testEquals()
    {
        $all = [$this->server11, $this->server_1, $this->server1_];
        $copies = [];
        foreach ($all as $k => $v) {
            $copies[$k] = new Type($v->getId(), $v->getName());
        }

        foreach ($all as $k => $v) {
            foreach ($copies as $j => $w) {
                $this->checkEquals($j === $k, $v, $w);
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

}
