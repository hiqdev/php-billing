<?php
declare(strict_types=1);
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\event;

use hiqdev\php\billing\charge\ChargeInterface;
use League\Event\AbstractEvent;
use DateTimeImmutable;

class InstallmentWasCharged extends AbstractEvent implements \JsonSerializable
{
    private function __construct(
        private readonly ChargeInterface $charge,
        private readonly DateTimeImmutable $time
    ) {
    }

    public static function onCharge(ChargeInterface $charge, DateTimeImmutable $time): self
    {
        return new self($charge, $time);
    }

    public function getCharge(): ChargeInterface
    {
        return $this->charge;
    }

    public function getTime(): \DateTimeImmutable
    {
        return $this->time;
    }

    public function jsonSerialize(): array
    {
        return [
            'price_id' => $this->charge->getPrice()->getId(),
            'part_id' => $this->charge->getPrice()->getTarget()->getId(),
        ];
    }
}
