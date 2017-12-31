<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\target;

use hiqdev\php\billing\target\TargetCollection;
use hiqdev\php\billing\target\Target;

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

    public function testEquals()
    {
        $atarget = new Target(null,       null);
        $aserver = new Target(null,       'server');
        $server1 = new Target($this->id1, 'server');
        $server2 = new Target($this->id2, 'server');
        $servers = new TargetCollection([$this->server1, $this->server2]);
        $adomain = new Target(null,       'domain');
        $domain1 = new Target($this->id1, 'domain');
        $domain2 = new Target($this->id2, 'domain');
        $domains = new TargetCollection([$this->domain1, $this->domain2]);

        $this->checkEquals([
            $this->atarget, $atarget,
            $this->aserver, $aserver,
            $this->server1, $server1,
        ]);
        $this->checkEquals([
            $this->atarget, $atarget,
            $this->aserver, $aserver,
            $this->server2, $server2,
        ]);

        $this->assertFalse($this->server1->equals($this->server2));
        $this->assertFalse($this->server2->equals($this->server1));

        $this->assertFalse($this->server1->equals($this->domain1));
        $this->assertFalse($this->server2->equals($this->domain2));
    }

    protected function checkEquals(array $targets, bool $equals = true) {
        foreach ($targets as $k => $v) {
            foreach ($targets as $j => $w) {
                if ($k === $j) {
                    $this->assertTrue($v->equals($w));
                } else {
                    $this->assertSame($equals, $v->equals($w));
                }
            }
        }
    }
}
