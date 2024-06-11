<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Currency;
use Money\Money;

class ProgressivePrice extends AbstractPrice
{
    /* @psalm-var array{array{
     * "price": numeric,
     * "currency": string,
     * "value": numeric,
     * }} $condition
     */
    protected array $condition;
    /**
     * @var QuantityInterface prepaid quantity also implies Unit
     * XXX cannot be null cause Unit is required
     */
    protected $prepaid;

    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        QuantityInterface $prepaid,
        array $condition,
        PlanInterface $plan = null,
    ) {
        parent::__construct($id, $type, $target, $plan);
        $this->prepaid = $prepaid;
        $this->condition = $condition;
    }

    public function getPrepaid()
    {
        return $this->prepaid;
    }

    public function getCondition(): array
    {
        return $this->condition;
    }

    public function setCondition(string $key, array $condition): self
    {
        $this->condition[] = $condition;
        return $this;
    }

    private function prepareCondition(): void
    {
        usort($this->condition, function($a, $b)
            {
                return $b['value'] <=> $a['value'];
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function calculateUsage(QuantityInterface $quantity): ?QuantityInterface
    {
        $usage = $quantity->convert($this->prepaid->getUnit())->subtract($this->prepaid);

        if ($usage->isPositive()) {
            return $usage;
        }

        return Quantity::create($this->prepaid->getUnit()->getName(), 0);
    }

    /**
     * @inheritDoc
     */
    public function calculatePrice(QuantityInterface $quantity): ?Money
    {
        $result = null;
        $this->prepareCondition();
        $usage = $this->calculateUsage($quantity);
        $quantity = $usage->getQuantity();
        foreach ($this->condition as $key => $condition) {
            if  ($condition['value'] < $quantity) {
                if ($key !== count($this->condition) - 1) {
                    $boundary = $quantity - $condition['value'];
                    $result += $boundary * $condition['price'];
                    $quantity = $quantity - $boundary;
                } else {
                    $result += $quantity * $condition['price'];
                }
            }
        }
        return new Money((int)($result * 100), new Currency($condition['currency']));
    }

    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        return $this->calculatePrice($quantity);
    }
}
