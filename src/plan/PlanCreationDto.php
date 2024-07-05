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
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class PlanCreationDto
{
    public $id;

    public $name;

    public $seller;

    /** @var array|null */
    public $prices;

    /// XXX should not be here
    public $is_grouping;

    public $type;

    public $parent;
}
