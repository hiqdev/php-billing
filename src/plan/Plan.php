<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\plan;

use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\price\PriceInterface;

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
    protected $prices;

    /**
     * @param int $id
     * @param string $name
     * @param PriceInterface[] $prices
     */
    public function __construct(
                            $id,
                            $name,
        CustomerInterface $seller = null,
                            $prices = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->seller = $seller;
        $this->prices = $prices;
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
    public function getName()
    {
        return $this->name;
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

    /**
     * @return bool
     */
    public function hasPrices()
    {
        return $this->prices !== null;
    }

    /**
     * @return PriceInterface[]|null
     */
    public function getPrices(): ?array
    {
        return $this->prices;
    }

    /**
     * @param PriceInterface[] $prices
     * @throws \Exception
     */
    public function setPrices(array $prices)
    {
        if ($this->hasPrices()) {
            throw new CannotReassignException('plan prices');
        }
        $this->prices = $prices;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
