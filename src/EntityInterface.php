<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * Basic interface for all entities.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface EntityInterface extends \JsonSerializable
{
    /**
     * @return int|string
     */
    public function getId();
}
