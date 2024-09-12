<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
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
    /**
     * @var null - any type can be used as ID or type name
     */
    const null ANY  = null;

    const float NONE = INF;

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
     */
    public function getName(): ?string;

    /**
     * @param TypeInterface $other other type to match against
     */
    public function equals(self $other): bool;

    /**
     * @param TypeInterface $other other type to match against
     */
    public function matches(self $other): bool;

    public function isDefined(): bool;

    public function isMonthly(): bool;

    public function belongsToGroup(string $group): bool;

    public function groupName(): string;

    public function belongsToLocalCategory(string $local): bool;

    public function localName(): string;
}
