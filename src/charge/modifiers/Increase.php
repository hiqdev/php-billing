<?php

declare(strict_types=1);
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2023, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

/**
 * Class Increase
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Increase extends Modifier
{
    public function __construct(array $addons = [])
    {
        parent::__construct($addons);
    }

    public function grows($step, $min = null): GrowingDiscount
    {
        $increase = new GrowingDiscount($step, $min, $this->addons);
        $increase->replaceAddon(GrowingDiscount::STEP, $increase->getStep()->inverted());

        return $increase;
    }
}
