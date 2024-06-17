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
    /* @var ProgressivePriceConditionDto[] $thresholds */
    protected array $thresholds;
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
        array $thresholds,
        ?PlanInterface $plan = null
    ) {
        parent::__construct($id, $type, $target, $plan);
        $this->prepaid = $prepaid;
        $this->thresholds = $thresholds;
    }

    public function getPrepaid()
    {
        return $this->prepaid;
    }

    public function getThresholds(): array
    {
        return $this->thresholds;
    }

    private function prepareThresholds(): void
    {
        usort($this->thresholds, function($a, $b)
            {
                return $b->value <=> $a->value;
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
        $this->prepareThresholds();
        $usage = $this->calculateUsage($quantity);
        $quantity = $usage->getQuantity();
        foreach ($this->thresholds as $key => $threshold) {
            if  ($threshold->value < $quantity) {
                if ($key !== count($this->thresholds) - 1) {
                    $boundary = $quantity - $threshold->value;
                    $result += $boundary * $threshold->price;
                    $quantity = $quantity - $boundary;
                } else {
                    $result += $quantity * $threshold->price;
                }
            }
        }
        return new Money((int)($result * 100), $threshold->currency);
    }

    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        return $this->calculatePrice($quantity);
    }
}
