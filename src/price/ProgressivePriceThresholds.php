<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\units\Quantity;
use InvalidArgumentException;
use Money\Money;

final class ProgressivePriceThresholds
{
    /** @var ProgressivePriceThreshold[] */
   private array $thresholds;

    /**
     * @param ProgressivePriceThreshold[] $thresholds
     */
   public function __construct(array $thresholds)
   {
       foreach ($thresholds as $threshold) {
           $this->add(ProgressivePriceThreshold::createFromScalar(
                   $threshold['price'],
                   $threshold['currency'],
                   $threshold['quantity'],
                   $threshold['unit'],
               )
           );
       }
   }

   public function add(ProgressivePriceThreshold $threshold): void
   {
      $this->checkCurrency($threshold->price());
      $this->checkUnit($threshold->quantity());
      $this->appendThresholds($threshold);
   }

    public function get(): array
    {
        $this->prepareThresholds();
        return $this->thresholds;
    }

    private function prepareThresholds(): void
    {
        usort($this->thresholds, function (ProgressivePriceThreshold $a, ProgressivePriceThreshold $b) {
            if ($b->quantity()->equals($a->quantity())) {
                return $b->price()->getAmount() <=> $a->price()->getAmount();
            }
            return $b->quantity()->getQuantity() <=> $a->quantity()->getQuantity();
        });
    }

    private function checkCurrency(Money $price): void
    {
        if (empty($this->thresholds)) {
            return;
        }

        $last = $this->thresholds[array_key_last($this->thresholds)];

        if (!$last->price()->getCurrency()->equals($price->getCurrency())
        ) {
            throw new InvalidArgumentException(sprintf(
                "Progressive price with threshold currency %s is not valid to other threshold currency %s",
                $last->price()->getCurrency()->getCode(),
                $price->getCurrency()->getCode()
            ));
        }
    }

    private function checkUnit(Quantity $prepaid): void
    {
        if (empty($this->thresholds)) {
            return;
        }

        $last = $this->thresholds[array_key_last($this->thresholds)];

        if (!$last->quantity()->getUnit()->isConvertible($prepaid->getUnit())) {
            throw new InvalidArgumentException(sprintf(
                "Progressive price with threshold unit %s is not convertible to other threshold unit %s",
                $last->quantity()->getUnit()->getName(),
                $prepaid->getUnit()->getName()
            ));
        }
    }

    private function appendThresholds(ProgressivePriceThreshold $threshold): void
    {
        $this->thresholds[] = $threshold;
    }
}
