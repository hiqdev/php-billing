<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
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

    protected function setUp()
    {
        $this->atarget = new Target(null,       null);
        $this->target1 = new Target($this->id1, null);
        $this->target2 = new Target($this->id2, null);
        $this->aserver = new Target(null,       'server');
        $this->server1 = new Target($this->id1, 'server');
        $this->server2 = new Target($this->id2, 'server');
        $this->servers = new TargetCollection([$this->server1, $this->server2]);
        $this->adomain = new Target(null,       'domain');
        $this->domain1 = new Target($this->id1, 'domain');
        $this->domain2 = new Target($this->id2, 'domain');
        $this->domains = new TargetCollection([$this->domain1, $this->domain2]);
    }

    protected function tearDown()
    {
    }

    public function testGetUniqueId()
    {
        $this->assertSame(':',          $this->atarget->getUniqueId());
        $this->assertSame(':1',         $this->target1->getUniqueId());
        $this->assertSame(':2',         $this->target2->getUniqueId());
        $this->assertSame('server:',    $this->aserver->getUniqueId());
        $this->assertSame('server:1',   $this->server1->getUniqueId());
        $this->assertSame('server:2',   $this->server2->getUniqueId());
    }

    public function testEquals()
    {
        $all = [
            $this->atarget, $this->target1, $this->target2,
            $this->aserver, $this->server1, $this->server2,
            $this->adomain, $this->domain1, $this->domain2,
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
            $this->atarget, $this->copy($this->atarget),
            $this->aserver, $this->copy($this->aserver),
            $this->server1, $this->copy($this->server1),
            $this->servers, $this->copy($this->servers),
        ]);
        $this->checkMatches([
            $this->atarget, $this->copy($this->atarget),
            $this->aserver, $this->copy($this->aserver),
            $this->server2, $this->copy($this->server2),
            $this->servers, $this->copy($this->servers),
        ]);

        $this->checkDoesntMatch([
            $this->server1, $this->server2, $this->adomain,
        ]);

        $this->checkDoesntMatch([
            $this->domain1, $this->domain2, $this->servers,
        ]);
    }

    protected function checkDoesntMatch(array $targets)
    {
        $this->checkMatches($targets, false);
    }

    protected function checkMatches(array $targets, bool $expect = true)
    {
        foreach ($targets as $k => $v) {
            foreach ($targets as $j => $w) {
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
