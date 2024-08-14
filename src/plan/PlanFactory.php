<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\plan;

/**
 * Default plan factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class PlanFactory implements PlanFactoryInterface
{
    public function create(PlanCreationDto $dto): PlanInterface
    {
        return $this->createAnyPlan($dto);
    }

    protected function createAnyPlan(PlanCreationDto $dto, string $class = null): PlanInterface
    {
        $class = $class ?? Plan::class;

        return new $class(
            $dto->id,
            $dto->name,
            $dto->seller,
            $dto->prices ?? [],
            $dto->type ?? null
        );
    }
}
