<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

use hiqdev\php\billing\EntityInterface;

/**
 * Target - any thing participating in billing as:.
 *
 * - object being charged (domain, server, certificate)
 * - product being sold (premium product, ???)
 *
 * Provides target's:
 *
 * - type
 * - ID, unique only between targets of same type
 * - unique ID
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TargetInterface extends EntityInterface
{
    /**
     * Get target ID, unique only between targets of the same type.
     * @return int|string
     */
    public function getId();

    /**
     * Get target type.
     * @return string
     */
    public function getType();

    /**
     * Get target unique ID, globally unique. Used for comparison.
     * Must be formed as $type:$id.
     * E.g.: client:login, client:1, server:T1, server:9.
     * @return string
     */
    public function getUniqId();
}
