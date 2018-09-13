<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\type;

/**
 * Type - type of action, price, charge, bill.
 *
 * Provides:
 *
 * - data:
 *      - ID - unique
 *      - name - unique also!
 * - logic:
 *      - unique ID
 *      - type matching with ANY and NONE features
 *      - comparison
 *      - generalization
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TypeInterface extends \JsonSerializable
{
    const ANY  = null;
    const NONE = INF;

    /**
     * Returns type id.
     * @return int|string
     */
    public function getId();

    /**
     * Globally unique ID: e.g. ID or name.
     * @return int|string
     */
    public function getUniqueId();

    /**
     * Returns type name.
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param TypeInterface $other other type to match against
     * @return bool
     */
    public function equals(self $other): bool;

    /**
     * @param TypeInterface $other other type to match against
     * @return bool
     */
    public function matches(self $other): bool;
}
