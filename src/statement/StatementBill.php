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

    /** @var string */
    protected $from;

    public function __construct(
        $id,
        TypeInterface $type,
        DateTimeImmutable $time,
        Money $sum,
        QuantityInterface $quantity,
        CustomerInterface $customer,
        TargetInterface $target = null,
        PlanInterface $plan = null,
        array $charges = [],
        BillState $state = null,
        DateTimeImmutable $month,
        ?string $from = null
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
        $this->from         = $from;
    }

    public function getMonth(): DateTimeImmutable
    {
        return $this->month;
    }

    public function getFrom(): string
    {
        return $this->from ?? '';
    }
}
