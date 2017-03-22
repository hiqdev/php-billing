<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * Target - any thing participating in billing as:
 *
 * - object being charged (domain, server, certificate)
 * - product being sold (premium product, ???)
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TargetInterface
{
    /**
     * Get target globally unique ID. Used for comparison.
     * E.g.: 1, 2, client:login, client:1, server:T1, server:9
     * @return string
     */
    public function getId();
}
