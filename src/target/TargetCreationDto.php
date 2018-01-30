<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class TargetCreationDto
{
    /** @var string|int|null */
    public $id;

    /** @var string */
    public $type;

    /** @var string */
    public $name;
}
