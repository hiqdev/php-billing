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

use hiqdev\php\billing\EntityInterface;

/**
 * Target - any thing participating in billing as:.
 *
 * - object being charged (domain, server, certificate)
 * - product being sold (premium product, domain zone, certificate type)
 *
 * Provides:
 *
 * - data:
 *      - ID, unique only between targets of same type
 *      - type
 *      - name
 * - logic:
 *      - unique ID
 *      - target matching with ANY and NONE features
 *      - comparison
 *      - generalization
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TargetInterface extends EntityInterface
{
    const ANY = null;
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
     * Get target state.
     * @return string
     */
    public function getState();

    /**
     * Get target name.
     * @return string
     */
    public function getName();

    /**
     * Get target full name: `type:name`
     */
    public function getFullName(): string;

    /**
     * Get target unique ID, unique among all targets. Used for comparison.
     * Could be formed like $type:$id.
     * E.g.: client:login, client:1, server:T1, server:9.
     * @return string
     */
    public function getUniqueId();

    /**
     * Checks, whether this target matches $other.
     */
    public function matches(TargetInterface $other): bool;

    /**
     * One way check matches. For internal use only.
     * Use `matches`.
     */
    public function checkMatches(TargetInterface $other): bool;

    /**
     * Checks whether $other target is the same as current one
     */
    public function equals(TargetInterface $other): bool;
}
