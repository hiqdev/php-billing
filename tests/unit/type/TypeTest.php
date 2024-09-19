<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
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

    private Type $nonenone;

    private Type $server11;
    private Type $server12;
    private Type $server_1;
    private Type $server1_;
    private Type $serverN1;
    private Type $server1N;
    private Type $serverN_;
    private Type $server_N;
    private Type $server22;
    private Type $server21;
    private Type $server_2;
    private Type $server2_;
    private Type $serverN2;
    private Type $server2N;

    private Type $domain11;
    private Type $domain_1;
    private Type $domain1_;
    private Type $domainN1;
    private Type $domain1N;
    private Type $domain22;
    private Type $domain_2;
    private Type $domain2_;

    protected function setUp(): void
    {
        $this->nonenone = new Type(Type::NONE,   Type::NONE);

        $this->server11 = new Type($this->sid1,  $this->sop1);
        $this->server12 = new Type($this->sid1,  $this->sop2);
        $this->server_1 = new Type(Type::ANY,    $this->sop1);
        $this->server1_ = new Type($this->sid1,  Type::ANY);
        $this->serverN1 = new Type(Type::NONE,   $this->sop1);
        $this->server1N = new Type($this->sid1,  Type::NONE);
        $this->serverN_ = new Type(Type::NONE,   Type::ANY);
        $this->server_N = new Type(Type::ANY,    Type::NONE);
        $this->server22 = new Type($this->sid2,  $this->sop2);
        $this->server21 = new Type($this->sid2,  $this->sop1);
        $this->server_2 = new Type(Type::ANY,    $this->sop2);
        $this->server2_ = new Type($this->sid2,  Type::ANY);
        $this->serverN2 = new Type(Type::NONE,   $this->sop2);
        $this->server2N = new Type($this->sid2,  Type::NONE);

        $this->domain11 = new Type($this->did1,  $this->dop1);
        $this->domain_1 = new Type(Type::ANY,    $this->dop1);
        $this->domain1_ = new Type($this->did1,  Type::ANY);
        $this->domainN1 = new Type(Type::NONE,   $this->dop1);
        $this->domain1N = new Type($this->did1,  Type::NONE);
        $this->domain22 = new Type($this->did2,  $this->dop2);
        $this->domain_2 = new Type(Type::ANY,    $this->dop2);
        $this->domain2_ = new Type($this->did2,  Type::ANY);
    }

    protected function copy(TypeInterface $type): TypeInterface
    {
        return new Type($type->getId(), $type->getName());
    }

    public function testGetUniqueId(): void
    {
        $this->assertSame($this->sop1,  $this->server11->getUniqueId());
        $this->assertSame($this->sop1,  $this->server_1->getUniqueId());
        $this->assertSame($this->sid1,  $this->server1_->getUniqueId());
        $this->assertSame($this->sop2,  $this->server22->getUniqueId());
        $this->assertSame($this->sop2,  $this->server_2->getUniqueId());
        $this->assertSame($this->sid2,  $this->server2_->getUniqueId());
    }

    public function testEquals(): void
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

    protected function checkEquals(bool $expect, $lhs, $rhs): void
    {
        $check = $lhs->equals($rhs);
        /*if ($check !== $expect) {
            var_dump('no equality', $expect, $lhs, $rhs);
        }*/
        $this->assertSame($expect, $check);
    }

    public function testMatches(): void
    {
        $this->checkMatches([$this->server11, $this->server_1]);
        $this->checkMatches([$this->server_1, $this->serverN1], false);
        $this->checkMatches([$this->server11, $this->server1_, $this->server12, $this->server1N]);

        $this->checkDoesntMatch([
            $this->server1N, $this->server2N, $this->serverN_,
            $this->serverN1, $this->serverN2, $this->server_N,
        ]);

        $this->checkDoesntMatch([
            $this->server11, $this->server_2, $this->serverN1,
            $this->domain11, $this->domain_2, $this->domainN1,
        ]);

        $this->checkDoesntMatch([
            $this->server_1, $this->server1_, $this->server_2, $this->server2_,
            $this->domain_1, $this->domain1_, $this->domain_2, $this->domain2_,
        ]);
    }

    protected function checkDoesntMatch(array $types): void
    {
        foreach ($types as $k => $v) {
            foreach ($types as $j => $w) {
                if ($k !== $j) {
                    $this->checkSingleMatch(false, $v, $w);
                }
            }
        }
    }

    protected function checkMatches(array $types, bool $self = true): void
    {
        $all = $types;
        if ($self) {
            foreach ($types as $type) {
                $all[] = $this->copy($type);
            }
        }
        foreach ($all as $k => $v) {
            foreach ($all as $j => $w) {
                if ($self || $k !== $j) {
                    $this->checkSingleMatch(true, $v, $w);
                }
            }
        }
    }

    protected function checkSingleMatch(bool $expect, $lhs, $rhs): void
    {
        $check = $lhs->matches($rhs);
        /*if ($check !== $expect) {
            var_dump('no match', $expect, $lhs, $rhs);
        }*/
        $this->assertSame($expect, $check);
    }

    public function testGetId(): void
    {
        $type = new Type(1, 'monthly');
        $this->assertEquals(1, $type->getId());

        $type = new Type('customId', 'discount');
        $this->assertEquals('customId', $type->getId());
    }

    public function testGetName(): void
    {
        $type = new Type(1, 'monthly');
        $this->assertEquals('monthly', $type->getName());

        $type = new Type(2);
        $this->assertNull($type->getName());
    }

    public function testHasName(): void
    {
        $type = new Type(1, 'monthly');
        $this->assertTrue($type->hasName());

        $type = new Type(2, '');
        $this->assertFalse($type->hasName());

        $type = new Type(3, TypeInterface::ANY);
        $this->assertFalse($type->hasName());

        $type = new Type(4, TypeInterface::NONE);
        $this->assertFalse($type->hasName());
    }

    public function testJsonSerialize(): void
    {
        $type = new Type(1, 'monthly');
        $expectedJson = [
            'id' => 1,
            'name' => 'monthly',
        ];
        $this->assertEquals($expectedJson, $type->jsonSerialize());
    }

    public function testIsDefined(): void
    {
        $type = new Type(1, 'monthly');
        $this->assertTrue($type->isDefined());

        $type = new Type(null, null);
        $this->assertFalse($type->isDefined());
    }

    public function testTypeWithAnyIdHasAnyId(): void
    {
        $type = Type::anyId('monthly,leasing');

        $this->assertSame(TypeInterface::ANY, $type->getId());
        $this->assertSame('monthly,leasing', $type->getName());
    }
}
