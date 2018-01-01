<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\plan;

/**
 * Plan factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface PlanFactoryInterface
{
    /**
     * Creates plan object.
     * @return Plan
     */
    public function create(PlanCreationDto $dto);
}
