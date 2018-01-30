<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

use hiqdev\php\billing\EntityInterface;

/**
 * Target - any thing participating in billing as:.
 *
 * - object being charged (domain, server, certificate)
 * - product being sold (premium product, domain zone, certificate type)
 *
 * Provides:
 *
 * - type
 * - ID, unique only between targets of same type
 * - unique ID
 * - target matching with ANY and NONE features
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TargetInterface extends EntityInterface
{
    const ANY =  null;
    const NONE = INF;

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
     * Get target name.
     * @return string
     */
    public function getName();

    /**
     * Get target unique ID, unique among all targets. Used for comparison.
     * Could be formed like $type:$id.
     * E.g.: client:login, client:1, server:T1, server:9.
     * @return string
     */
    public function getUniqueId();

    /**
     * Checks, whether this target mathes $other.
     * @param TargetInterface $other
     * @return bool
     */
    public function matches(self $other): bool;

    /**
     * One way check matches. For internal use only.
     * Use `matches`.
     * @param TargetInterface $other
     * @return bool
     */
    public function checkMatches(self $other): bool;
}
