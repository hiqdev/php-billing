<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

interface PriceWithThresholdsInterface
{
    public function getThresholds(): ProgressivePriceThresholdList;
}
