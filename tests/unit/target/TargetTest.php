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
    protected function setUp()
    {
        $this->aserver = new Target(null, 'server');
        $this->server1 = new Target(1111, 'server');
        $this->server2 = new Target(2222, 'server');
        $this->servers = new TargetCollection([$this->server1, $this->server2]);
        $this->adomain = new Target(null, 'domain');
        $this->domain1 = new Target(1111, 'domain');
        $this->domain2 = new Target(2222, 'domain');
        $this->domains = new TargetCollection([$this->domain1, $this->domain2]);
    }

    protected function tearDown()
    {
    }

    public function testEquals()
    {
        $aserver = new Target(null, 'server');
        $server1 = new Target(1111, 'server');
        $server2 = new Target(2222, 'server');
        $servers = new TargetCollection([$this->server1, $this->server2]);
        $adomain = new Target(null, 'domain');
        $domain1 = new Target(1111, 'domain');
        $domain2 = new Target(2222, 'domain');
        $domains = new TargetCollection([$this->domain1, $this->domain2]);

        $this->assertTrue($this->server1->equals($server1));
        $this->assertTrue($server1->equals($this->server1));

        $this->assertFalse($this->server1->equals($this->server2));
        $this->assertFalse($this->server2->equals($this->server1));
    }
}
