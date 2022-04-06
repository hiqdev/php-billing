<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\Exception;

use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\ExceptionInterface;
use Exception;
use Yii;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ChargeOverlappingException extends Exception implements ExceptionInterface
{
    private ChargeInterface $currentCharge;

    private ChargeInterface $previousCharge;

    public static function forCharge(ChargeInterface $charge, ChargeInterface $previous): self
    {
        $self = new self('Charge being saved overlaps a previously saved one');
        $self->currentCharge = $charge;
        $self->previousCharge = $previousa;

        return $self;
    }

    public function getChargeId(): string
    {
        return (string) $this->currentCharge->getId();
    }

    public function getPreviousCharge(): ChargeInterface
    {
        return $this->previousCharge;
    }

    public function getCurrentCharge(): ChargeInterface
    {
        return $this->currentCharge;
    }
}
