<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TargetRepositoryInterface
{
    /**
     * @param $specification
     * @return Target|false
     */
    public function findOne($specification);

    public function save(TargetInterface $target): void;
}
