<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2021, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\statement;

use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\bill\BillState;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\units\QuantityInterface;
use DateTimeImmutable;
use Money\Money;

/**
 * StatementBill.
 *
 * @author Yurii Myronchuk <bladeroot@gmail.com>
 */
class StatementBill extends Bill implements StatementBillInterface, BillInterface
{
    /** @var DateTimeImmutable */
    protected $month;

    /** @var Money */
    protected $price;

    /** @var Money */
    protected $overuse;

    /** @var QuantityInterface */
    protected $prepaid;

    /** @var string */
    protected $from;

    /** @var TypeInterface */
    protected $tariff_type;

    public function __construct(
        $id,
        TypeInterface $type,
        DateTimeImmutable $time,
        Money $sum,
        QuantityInterface $quantity,
        CustomerInterface $customer,
        DateTimeImmutable $month,
        Money $price = null,
        Money $overuse = null,
        QuantityInterface $prepaid = null,
        array $charges = [],
        TargetInterface $target = null,
        PlanInterface $plan = null,
        BillState $state = null,
        ?string $from = null,
        TypeInterface $tariff_type = null
    ) {
        parent::__construct(
            $id,
            $type,
            $time,
            $sum,
            $quantity,
            $customer,
            $target,
            $plan,
            $charges,
            $state
        );
        $this->month        = $month;
        $this->price        = $price;
        $this->overuse      = $overuse;
        $this->prepaid      = $prepaid;
        $this->from         = $from;
        $this->tariff_type   = $tariff_type;
    }

    public function getMonth(): DateTimeImmutable
    {
        return $this->month;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function getPrice(): ?Money
    {
        return $this->price;
    }

    public function getOveruse(): ?Money
    {
        return $this->overuse;
    }

    public function getPrepaid(): QuantityInterface
    {
        return $this->prepaid;
    }

    public function getTariffType(): ?TypeInterface
    {
        return $this->tariffType;
    }
}
