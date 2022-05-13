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
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\type\TypeInterface;

/**
 * Tariff Plan.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Plan implements PlanInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Plan|null
     * XXX not sure to implement
     */
    protected $parent;

    /**
     * @var CustomerInterface
     */
    protected $seller;

    /**
     * @var PriceInterface[]
     */
    protected $prices = [];

    /**
     * @var ?TypeInterface
     */
    protected $type;

    /**
     * @param int|string|null $id
     * @param string $name
     * @param PriceInterface[] $prices
     */
    public function __construct(
        $id,
        $name,
        CustomerInterface $seller = null,
        $prices = [],
        TypeInterface $type = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->seller = $seller;
        $this->prices = $prices;
        $this->type = $type;
    }

    public function getUniqueId()
    {
        return $this->getId();
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return CustomerInterface
     */
    public function getSeller(): ?CustomerInterface
    {
        return $this->seller;
    }

    public function getParent(): ?PlanInterface
    {
        return $this->parent;
    }

    public function hasPrices(): bool
    {
        return $this->prices !== [];
    }

    /**
     * @return PriceInterface[]
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    public function setPrices(array $prices): void
    {
        if ($this->hasPrices()) {
            throw new CannotReassignException('plan prices');
        }
        $this->prices = $prices;
    }

    public function getType(): ?TypeInterface
    {
        return $this->type ?? null;
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
