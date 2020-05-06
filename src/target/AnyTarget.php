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
class AnyTarget extends AbstractTarget
{
    private static $instance;

    public function __construct()
    {
        parent::__construct(self::ANY, self::ANY);
    }

    public static function get(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
