<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\customer;

use hiqdev\php\billing\EntityInterface;

/**
 * Customer interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface CustomerInterface extends EntityInterface
{
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

    /**
     * Get Customer state.
     * @return null|string
     */
    public function getState(): ?string;

    public function isDeleted(): bool;
}
