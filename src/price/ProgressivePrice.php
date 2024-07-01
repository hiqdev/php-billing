<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class ProgressivePrice extends AbstractPrice
{
    protected ProgressivePriceThresholds $thresholds;

    protected Money $price;

    protected QuantityInterface $prepaid;

    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        QuantityInterface $prepaid,
        Money $price,
        ProgressivePriceThresholds $thresholds,
        ?PlanInterface $plan = null
    ) {
        parent::__construct($id, $type, $target, $plan);
        $this->thresholds = $thresholds;
        $this->price = $price;
        $this->prepaid = $prepaid;
    }

    public function getThresholds(): ProgressivePriceThresholds
    {
        return $this->thresholds;
    }

    public function getPrepaid(): QuantityInterface
    {
        return $this->prepaid;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    /**
     * @inheritDoc
     */
    public function calculateUsage(QuantityInterface $quantity): ?QuantityInterface
    {
        $usage = $quantity->subtract($this->prepaid);

        if ($usage->isPositive()) {
            return $quantity;
        }

        return Quantity::create($this->prepaid->getUnit()->getName(), 0);
    }

    /**
     * @inheritDoc
     */
    public function calculatePrice(QuantityInterface $quantity): ?Money
    {
        return $this->price;
    }

    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        $result = $this->price->multiply(0);
        $remainingUsage = $this->calculateUsage($quantity);
        $totalBilledUsage = $this->prepaid;
        $thresholds = $this->thresholds->withAdded(
            ProgressivePriceThreshold::createFromObjects($this->price, $this->prepaid)
        )->get();

        foreach ($thresholds as $threshold) {
            $quantity = $threshold->quantity();
            if ($quantity->compare($remainingUsage) >= 0) {
                $quantity = $remainingUsage;
            }
            $billedUsage = $remainingUsage->subtract($quantity);
            $price = $threshold->price();

            $chargedAmount = $price->money()
                ->multiply((string)$billedUsage->getQuantity())
                ->divide((string)($price->multiplier()));

// Uncomment to see and debug intermediate calculation steps
//            $debugTrace[] = [
//                'threshold' => $threshold->quantity()->getQuantity(),
//                'billedQuantity' => $quantity->getQuantity(),
//                'billed' => $billedUsage->getQuantity(),
//                'price' => $price->money()->getAmount(),
//                'charged' => $chargedAmount->getAmount(),
//            ];

            $result = $result->add($chargedAmount);
            $remainingUsage = $remainingUsage->subtract($billedUsage);
            $totalBilledUsage = $totalBilledUsage->add($billedUsage);
        }

        return $result;
    }
}
