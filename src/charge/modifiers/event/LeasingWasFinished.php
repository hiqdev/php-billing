<?php

namespace hiqdev\php\billing\charge\modifiers\event;

use hiqdev\php\billing\price\PriceInterface;
use League\Event\AbstractEvent;

class LeasingWasFinished extends AbstractEvent
{
    /**
     * @var PriceInterface
     */
    private $price;
    /**
     * @var \DateTimeImmutable
     */
    private $date;

    private function __construct(PriceInterface $price, \DateTimeImmutable $date)
    {
        $this->price = $price;
        $this->date = $date;
    }

    public static function forPriceInMonth(PriceInterface $price, \DateTimeImmutable $date): self
    {
        return new self($price, $date);
    }

    /**
     * @return PriceInterface
     */
    public function getPrice(): PriceInterface
    {
        return $this->price;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
}
