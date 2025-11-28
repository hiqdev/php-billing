<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\plan;

use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\EntityInterface;
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\type\TypeInterface;

/**
 * Plan Interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface PlanInterface extends EntityInterface
{
    /**
     * @return int|string|null
     */
    public function getId();

    public function setId(int $id): void;

    /**
     * Globally unique ID.
     * @return int|string
     */
    public function getUniqueId();

    /**
     * @return PriceInterface[]
     */
    public function getPrices(): array;

    public function hasPrices(): bool;

    /**
     * @param PriceInterface[] $prices
     * @throws CannotReassignException when prices are already set
     */
    public function setPrices(array $prices): void;

    public function getSeller(): ?CustomerInterface;

    public function getName(): string;

    public function setName(string $name): void;

    public function getType(): ?TypeInterface;
    public function getParentId(): ?int;
    public function setParentId(int $parentId): void;
}
