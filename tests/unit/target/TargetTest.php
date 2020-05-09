<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\target;

use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\target\TargetCollection;
use hiqdev\php\billing\target\TargetInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class TargetTest extends \PHPUnit\Framework\TestCase
{
    protected $id1 = 1;
    protected $id2 = 2;
    protected $idA = 8;
    protected $idB = 9;

    protected function setUp()
    {
        $this->target_ = Target::any();
        $this->target1 = new Target($this->id1,     Target::ANY);
        $this->target2 = new Target($this->id2,     Target::ANY);
        $this->targetA = new Target($this->idA,     Target::ANY, 'A');
        $this->targetB = new Target($this->idB,     Target::ANY, 'B');
        $this->targets = new TargetCollection([$this->target1, $this->target2]);
        $this->server_ = new Target(Target::ANY,    'server');
        $this->server1 = new Target($this->id1,     'server');
        $this->server2 = new Target($this->id2,     'server');
        $this->serverA = new Target($this->idA,     'server', 'A');
        $this->serverB = new Target($this->idB,     'server', 'B');
        $this->serverN = new Target(Target::NONE,   'server');
        $this->servers = new TargetCollection([$this->server1, $this->server2]);
        $this->domain_ = new Target(Target::ANY,    'domain');
        $this->domain1 = new Target($this->id1,     'domain');
        $this->domain2 = new Target($this->id2,     'domain');
        $this->domainN = new Target(Target::NONE,   'domain');
        $this->domains = new TargetCollection([$this->domain1, $this->domain2]);
    }

    protected function tearDown()
    {
    }

    public function testGetFullName()
    {
        $this->assertSame('',           $this->target_->getFullName());
        $this->assertSame('',           $this->target1->getFullName());
        $this->assertSame('',           $this->target2->getFullName());
        $this->assertSame(':A',         $this->targetA->getFullName());
        $this->assertSame(':B',         $this->targetB->getFullName());
        $this->assertSame('',           $this->targets->getFullName());
        $this->assertSame('server:',    $this->server_->getFullName());
        $this->assertSame('server:',    $this->server1->getFullName());
        $this->assertSame('server:',    $this->server2->getFullName());
        $this->assertSame('server:A',   $this->serverA->getFullName());
        $this->assertSame('server:B',   $this->serverB->getFullName());
        $this->assertSame('server:',    $this->servers->getFullName());
    }

    public function testGetUniqueId()
    {
        $this->assertSame(':',          $this->target_->getUniqueId());
        $this->assertSame(':1',         $this->target1->getUniqueId());
        $this->assertSame(':2',         $this->target2->getUniqueId());
        $this->assertSame(':8',         $this->targetA->getUniqueId());
        $this->assertSame(':9',         $this->targetB->getUniqueId());
        $this->assertSame(':1',         $this->targets->getUniqueId());
        $this->assertSame('server:',    $this->server_->getUniqueId());
        $this->assertSame('server:1',   $this->server1->getUniqueId());
        $this->assertSame('server:2',   $this->server2->getUniqueId());
        $this->assertSame('server:8',   $this->serverA->getUniqueId());
        $this->assertSame('server:9',   $this->serverB->getUniqueId());
        $this->assertSame('server:1',   $this->servers->getUniqueId());
    }

    public function testEquals()
    {
        $all = [
            $this->target_, $this->target1, $this->target2,
            $this->server_, $this->server1, $this->server2,
            $this->domain_, $this->domain1, $this->domain2,
        ];
        $copies = [];
        foreach ($all as $k => $v) {
            $copies[$k] = $this->copy($v);
        }

        foreach ($all as $k => $v) {
            foreach ($copies as $j => $w) {
                $this->assertSame($j === $k, $v->equals($w));
                $this->assertSame($j === $k, $w->equals($v));
            }
        }
    }

    protected function copy(TargetInterface $target)
    {
        if ($target instanceof TargetCollection) {
            return new TargetCollection($target->getTargets());
        } else {
            return new Target($target->getId(), $target->getType());
        }
    }

    public function testMatches()
    {
        $this->checkMatches([
            $this->target_, $this->server_, $this->serverN,
        ], false);

        $this->checkMatches([
            $this->target_, $this->server_, $this->server1, $this->servers,
        ]);
        $this->checkMatches([
            $this->target_, $this->server_, $this->server2, $this->servers,
        ]);

        $this->checkDoesntMatch([
            $this->server1, $this->server2, $this->serverN, $this->domain_,
        ]);
        $this->checkDoesntMatch([
            $this->domain1, $this->domain2, $this->domainN, $this->servers,
        ]);

        $this->checkDoesntMatch([
            $this->server_, $this->domains, $this->domainN, $this->domainN,
        ]);
    }

    protected function checkMatches(array $targets, $self = true)
    {
        $all = $targets;
        if ($self) {
            foreach ($targets as $target) {
                $all[] = $this->copy($target);
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

    protected function checkDoesntMatch(array $targets)
    {
        foreach ($targets as $k => $v) {
            foreach ($targets as $j => $w) {
                if ($k !== $j) {
                    $this->checkSingleMatch(false, $v, $w);
                }
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
