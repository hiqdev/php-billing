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

class ProgressivePrice extends SinglePrice
{
    protected ProgressivePriceThresholds $thresholds;

    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        QuantityInterface $prepaid,
        Money $price,
        array $thresholds,
        ?PlanInterface $plan = null
    ) {
        parent::__construct($id, $type, $target, $plan, $prepaid, $price);
        $this->thresholds = new ProgressivePriceThresholds($thresholds);
    }

    /**
     * @return ProgressivePriceThresholds
     */
    public function getThresholds(): array
    {
        return $this->thresholds;
    }

    /**
     * @inheritDoc
     */
    public function calculateUsage(QuantityInterface $quantity): ?QuantityInterface
    {
        $usage = $quantity->convert($this->prepaid->getUnit());

        if ($usage->isPositive()) {
            return $usage;
        }

        return Quantity::create($this->prepaid->getUnit()->getName(), $quantity->getQuantity());
    }

    /**
     * @inheritDoc
     */
    public function calculatePrice(QuantityInterface $quantity): Money
    {
        $result = new Money(0, $this->price->getCurrency());
        $usage = $this->calculateUsage($quantity);
        $thresholds = $this->thresholds->get();
        $usageCur = null;
        foreach ($thresholds as $key => $threshold) {
            if (is_null($usageCur)) {
                $usageCur = PriceHelper::buildQuantityByMoneyPrice(
                    $threshold->getBasePrice(),
                    $usage->getUnit()->getName(),
                    (string)$usage->getQuantity()
                );
            }
            if  ($threshold->quantity()->compare($usageCur) < 0) {
                if ($key !== count($thresholds) - 1) {
                    $boundary = $usageCur->subtract($threshold->quantity());
                    $result = $result->add(new Money(
                            $boundary->multiply($threshold->price()->getAmount())->getQuantity(),
                            $threshold->price()->getCurrency()
                        )
                    );
                    $usageCur = $usageCur->subtract($boundary);
                } else {
                    $result = $result->add(new Money(
                            (int) $usage->multiply($threshold->price()->getAmount())->getQuantity(),
                            $threshold->price()->getCurrency()
                        )
                    );
                }
            }
        }
        return $result;
    }

    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        return $this->calculatePrice($quantity);
    }
}
