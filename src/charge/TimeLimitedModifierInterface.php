<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use DateTimeImmutable;
use hiqdev\php\billing\charge\modifiers\addons\Since;
use hiqdev\php\billing\charge\modifiers\addons\Till;
use hiqdev\php\billing\formula\FormulaRuntimeError;

interface TimeLimitedModifierInterface
{
    /**
     * @param DateTimeImmutable $time
     * @return bool whether the modifier is applicable at the passed $time
     * @throws FormulaRuntimeError when modifier is misconfigured
     */
    public function checkPeriod(DateTimeImmutable $time): bool;

    public function till($time);

    public function getTill(): ?Till;

    public function since($time);

    public function getSince(): ?Since;
}
