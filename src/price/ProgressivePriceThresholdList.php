<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\Money\MultipliedMoney;
use JsonSerializable;
use hiqdev\php\units\Quantity;
use InvalidArgumentException;

final class ProgressivePriceThresholdList implements JsonSerializable
{
    /** @var ProgressivePriceThreshold[] */
    private array $thresholds;

    /**
     * @param ProgressivePriceThreshold[] $thresholds
     */
    public function __construct(array $thresholds)
    {
        foreach ($thresholds as $threshold) {
            $this->checkCanBeAdded($threshold);
            $this->appendThresholds($threshold);
        }

        if (empty($this->thresholds)) {
            throw new InvalidArgumentException('Progressive price thresholds must not be empty');
        }
    }

    /**
     * @param array{
     *     price: numeric-string,
     *     currency: string,
     *     quantity: numeric-string,
     *     unit: string
     * } $thresholds
     */
    public static function fromScalarsArray(array $thresholds): self
    {
        return new self(array_map(function ($threshold) {
            return ProgressivePriceThreshold::createFromScalar(
                $threshold['price'],
                $threshold['currency'],
                $threshold['quantity'],
                $threshold['unit']
            );
        }, $thresholds));
    }

    private function checkCanBeAdded(ProgressivePriceThreshold $threshold): void
    {
        $this->checkCurrency($threshold->price());
        $this->checkUnit($threshold->quantity());
    }

    public function withAdded(ProgressivePriceThreshold $threshold): self
    {
        $this->checkCanBeAdded($threshold);

        $self = clone $this;
        $self->appendThresholds($threshold);

        return $self;
    }

    /**
     * @return ProgressivePriceThreshold[]
     */
    public function get(): array
    {
        $this->prepareThresholds();

        return $this->thresholds;
    }

    public function least(): ProgressivePriceThreshold
    {
        return $this->thresholds[0];
    }

    private function prepareThresholds(): void
    {
        usort($this->thresholds, function (ProgressivePriceThreshold $a, ProgressivePriceThreshold $b) {
            if ($b->quantity()->convert($a->quantity()->getUnit())->equals($a->quantity())) {
                return $b->price()->getAmount() <=> $a->price()->getAmount();
            }
            return $b->quantity()->convert($a->quantity()->getUnit())->getQuantity() <=> $a->quantity()->getQuantity();
        });
    }

    private function checkCurrency(MultipliedMoney $price): void
    {
        if (empty($this->thresholds)) {
            return;
        }

        $last = $this->thresholds[array_key_last($this->thresholds)];

        if (!$last->price()->getCurrency()->equals($price->getCurrency())) {
            throw new InvalidArgumentException(
                sprintf(
                    "Progressive price thresholds must have the same currency, last is %s, new is %s",
                    $last->price()->getCurrency()->getCode(),
                    $price->getCurrency()->getCode()
                )
            );
        }
    }

    private function checkUnit(Quantity $prepaid): void
    {
        if (empty($this->thresholds)) {
            return;
        }

        $last = $this->thresholds[array_key_last($this->thresholds)];

        if (!$last->quantity()->getUnit()->isConvertible($prepaid->getUnit())) {
            throw new InvalidArgumentException(
                sprintf(
                    "Progressive price thresholds must be of the same unit family, last is %s, new is %s",
                    $last->quantity()->getUnit()->getName(),
                    $prepaid->getUnit()->getName()
                )
            );
        }
    }

    private function appendThresholds(ProgressivePriceThreshold $threshold): void
    {
        $this->thresholds[] = $threshold;
    }

    public function __toArray(): array
    {
        $result = [];
        foreach ($this->thresholds as $threshold) {
            $result[] = $threshold->__toArray();
        }
        return $result;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->thresholds;
    }
}
