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

use Exception;
use hiqdev\php\billing\ExceptionInterface;
use Throwable;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ChargeOverlappingException extends Exception implements ExceptionInterface
{
    private string $chargeId;

    public static function forCharge(ChargeInterface $charge): self
    {
        $self = new self('Charge being saved overlaps a previously saved one');
        $self->chargeId = (string)$charge->getId();
        
        return $self;
    }

    public function getChargeId(): string
    {
        return $this->chargeId;
    }
}
