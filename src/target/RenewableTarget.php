<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

use DateTimeImmutable;

/**
 * Renewable Target - object being charged: domain, server
 * Holds: expires, initialExpires for renewal accounting.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class RenewableTarget extends AbstractTarget
{
    /**
     * @var DateTimeImmutable
     */
    protected $expires;

    /**
     * @var DateTimeImmutable
     */
    protected $initialExpires;

    /**
     * @var integer
     */
    protected $renewedNum;
}
