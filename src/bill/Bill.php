<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\bill;

use DateTimeImmutable;
use hiqdev\php\billing\action\UsageInterval;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Bill.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Bill implements BillInterface
{
    protected TypeInterface $type;

    protected DateTimeImmutable $time;

    protected Money $sum;

    protected QuantityInterface $quantity;

    protected CustomerInterface $customer;

    protected ?TargetInterface $target;

    protected ?BillRequisite $requisite = null;

    protected ?PlanInterface $plan;

    /** @var ChargeInterface[] */
    protected array $charges = [];

    protected ?BillState $state;

    protected string $comment = '';

    protected UsageInterval $usageInterval;

    public function __construct(
        protected int|string|null $id,
        TypeInterface $type,
        DateTimeImmutable $time,
        Money $sum,
        QuantityInterface $quantity,
        CustomerInterface $customer,
        ?TargetInterface $target = null,
        ?PlanInterface $plan = null,
        array $charges = [],
        ?BillState $state = null
    ) {
        $this->type         = $type;
        $this->time         = $time;
        $this->sum          = $sum;
        $this->quantity     = $quantity;
        $this->customer     = $customer;
        $this->target       = $target;
        $this->plan         = $plan;
        $this->charges      = $charges;
        $this->state        = $state;
    }

    /**
     * Provides unique string.
     * Can be used to compare or aggregate bills.
     */
    public function getUniqueString(): string
    {
        $parts = [
            'currency'  => $this->sum->getCurrency()->getCode(),
            'buyer'     => $this->customer->getUniqueId(),
            'target'    => $this->target ? $this->target->getUniqueId() : null,
            'type'      => $this->type->getUniqueId(),
            'time'      => $this->time->format('c'),
        ];

        return implode('-', $parts);
    }

    public function getUsageInterval(): UsageInterval
    {
        if (!isset($this->usageInterval)) {
            $this->initializeWholeMonthUsageInterval();
        }

        return $this->usageInterval;
    }

    private function initializeWholeMonthUsageInterval(): void
    {
        $this->setUsageInterval(UsageInterval::wholeMonth($this->time));
    }

    public function calculatePrice(): Money
    {
        $quantity = $this->quantity->getQuantity();

        return $quantity ? $this->sum->divide(sprintf('%.14F', $quantity)) : $this->sum;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function setId(int|string $id): void
    {
        if ($this->id === $id) {
            return;
        }
        if ($this->id !== null) {
            throw new CannotReassignException('bill id');
        }
        $this->id = $id;
    }

    public function getType(): TypeInterface
    {
        return $this->type;
    }

    public function getTime(): DateTimeImmutable
    {
        return $this->time;
    }

    public function getTarget(): ?TargetInterface
    {
        return $this->target;
    }

    public function getRequisite(): ?BillRequisite
    {
        return $this->requisite;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getQuantity(): QuantityInterface
    {
        return $this->quantity;
    }

    public function setQuantity(QuantityInterface $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSum(): Money
    {
        return $this->sum;
    }

    public function getPlan(): ?PlanInterface
    {
        return $this->plan;
    }

    public function hasCharges(): bool
    {
        return $this->charges !== [];
    }

    /**
     * @return ChargeInterface[]
     */
    public function getCharges(): array
    {
        return $this->charges;
    }

    /**
     * @param ChargeInterface[] $charges
     * @throws CannotReassignException
     */
    public function setCharges(array $charges): static
    {
        if ($this->hasCharges()) {
            throw new CannotReassignException('bill charges');
        }
        $this->charges = $charges;

        return $this;
    }

    public function getState(): ?BillState
    {
        return $this->state;
    }

    public function setFinished(): void
    {
        $this->state = BillState::finished();
    }

    public function isFinished(): ?bool
    {
        return $this->state === null ? null : $this->state->isFinished();
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }

    public function setUsageInterval(UsageInterval $usageInterval): void
    {
        $this->usageInterval = $usageInterval;
    }
}
