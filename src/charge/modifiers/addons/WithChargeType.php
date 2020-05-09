<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\addons;

use hiqdev\php\billing\charge\ChargeModifier;

/**
 * Trait WithChargeType
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait WithChargeType
{
    public function as(string $type): ChargeModifier
    {
        return $this->addAddon('chargeType', new ChargeType($type));
    }

    public function getChargeType(): ?ChargeType
    {
        return $this->getAddon('chargeType');
    }
}
