<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * Resource Type.
 */
class Type
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * Finds objects matching the resource type and given object.
     * Default behavior: just the object itself.
     * E.g.:
     * - for server these are all it's hardware parts.
     * - for domain these is it's zone.
     * @return object[]
     */
    public function findMatchingObjects()
    {
        return [$this->object];
    }
}
