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

namespace hiqdev\php\billing\tools;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\sale\SaleInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface FactoryInterface
{
    /**
     * Create billing object by entity name and data.
     */
    public function create(string $entity, $data);

    public function getSale($data): SaleInterface;

    public function getAction($data): ActionInterface;

    public function getPlan($data): PlanInterface;
}
