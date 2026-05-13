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
     * @var CustomerInterface
     */
    protected $seller;

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
        protected $id,
        protected $name,
        ?CustomerInterface $seller = null,
        protected $prices = [],
        ?TypeInterface $type = null,
        protected ?int $parent_id = null
    ) {
        $this->seller = $seller;
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

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function setParentId(int $parentId): void
    {
        $this->parent_id = $parentId;
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
