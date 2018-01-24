<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\type;

/**
 * Type interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TypeInterface extends \JsonSerializable
{
    const ANY  = null;
    const NONE = INF;

    /**
     * Returns type id.
     * @return int|string
     */
    public function getId();

    /**
     * Returns type name.
     * @return string
     */
    public function getName();

    /**
     * @param TypeInterface $other other type to match against
     * @return bool
     */
    public function equals(TypeInterface $other);

    /**
     * @param TypeInterface $other other type to match against
     * @return bool
     */
    public function matches(TypeInterface $other);
}
