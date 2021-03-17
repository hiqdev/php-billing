<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\sale;

/**
 * Default sale factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class SaleFactory implements SaleFactoryInterface
{
    /**
     * Creates sale object.
     * @return Sale
     */
    public function create(SaleCreationDto $dto)
    {
        $sale = new Sale($dto->id, $dto->target, $dto->customer, $dto->plan, $dto->time);

        if ($dto->closeTime !== null) {
            $sale->close($dto->closeTime);
        }

        return $sale;
    }
}
