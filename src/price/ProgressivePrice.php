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

class ProgressivePrice extends AbstractPrice implements PriceWithThresholdsInterface, PriceWithMoneyInterface, PriceWIthQuantityInterface
{
    protected ProgressivePriceThresholdList $thresholds;

    protected Money $price;

    protected QuantityInterface $prepaid;

    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        QuantityInterface $prepaid,
        Money $price,
        ProgressivePriceThresholdList $thresholds,
        ?PlanInterface $plan = null
    ) {
        parent::__construct($id, $type, $target, $plan);
        $this->thresholds = $thresholds;
        $this->price = $price;
        $this->prepaid = $prepaid;
    }

    public function getThresholds(): ProgressivePriceThresholdList
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

    /**
     * @var ProgressivePriceCalculationTrace[]
     */
    private array $calculationTraces = [];

    /**
     * @return ProgressivePriceCalculationTrace[]
     * @internal A debug method to see intermediate calculations
     * after the latest call to calculateSum()
     */
    public function getCalculationTraces(): array
    {
        return $this->calculationTraces;
    }

    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        $this->calculationTraces = [];

        $result = $this->price->multiply(0);
        $remainingUsage = $this->calculateUsage($quantity);
        if ($remainingUsage->getQuantity() === 0) {
            return $result;
        }

        $totalBilledUsage = $this->prepaid;
        $thresholds = $this->thresholds->withAdded(
            ProgressivePriceThreshold::createFromObjects($this->price, $this->prepaid)
        )->get();

        foreach ($thresholds as $threshold) {
            $quantity = $threshold->quantity();
            if ($quantity->compare($remainingUsage) >= 0) {
                $quantity = $remainingUsage;
            }
            $billedUsage = $remainingUsage->subtract($quantity)->convert($threshold->unit());
            $price = $threshold->price();

            $chargedAmount = $price->money()
                                   ->multiply((string)$billedUsage->getQuantity())
                                   ->divide((string)($price->multiplier()));

            $this->calculationTraces[] = new ProgressivePriceCalculationTrace(
                $threshold, $billedUsage, $chargedAmount
            );

            $result = $result->add($chargedAmount);
            $remainingUsage = $remainingUsage->subtract($billedUsage);
            $totalBilledUsage = $totalBilledUsage->add($billedUsage);
        }

        return $result;
    }
}
