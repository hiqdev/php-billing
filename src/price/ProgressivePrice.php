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
        /* @psalm-var array{
         *     array{
         *         'price': string,
         *         'currency': string,
         *         'quantity': string,
         *         'unit': string
         *     }
         * } $thresholds
         */
        array $thresholds,
        ?PlanInterface $plan = null
    ) {
        parent::__construct($id, $type, $target, $plan);
        $this->thresholds = new ProgressivePriceThresholds($thresholds);
        $this->price = $price;
        $this->prepaid = $prepaid;
    }

    public function getThresholds(): array
    {
        return $this->thresholds->__toArray();
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
        $usage = $quantity->convert($this->prepaid->getUnit());

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
        return $this->price;
    }

    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        $result = new Money(0, $this->price->getCurrency());
        $usage = $this->calculateUsage($quantity);
        $thresholds = $this->thresholds->get();
        foreach ($thresholds as $key => $threshold) {
            if  ($threshold->quantity()->compare($usage) < 0) {
                    $boundary = $usage->subtract($threshold->quantity());
                    $result = $result->add(new Money(
                            (int) $boundary->multiply($threshold->price()->getAmount())->getQuantity(),
                            $threshold->price()->getCurrency()
                        )
                    );
                    $usage = $usage->subtract($boundary);
            }
        }
        $result = (new DecimalMoneyParser(new ISOCurrencies()))->parse($result->getAmount(), $result->getCurrency());
        return $result->divide($this->thresholds->getPriceRate());
    }
}
