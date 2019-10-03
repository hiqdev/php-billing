<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\customer;

/**
 * Customer interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface CustomerInterface
{
    /**
     * Get ID.
     * @return int|string
     */
    public function getId();

    /**
     * Globally unique ID: e.g. ID or login.
     * @return int|string
     */
    public function getUniqueId();

    /**
     * Returns client login.
     * @return string
     */
    public function getLogin();

    /**
     * @return static|null
     */
    public function getSeller();
}
