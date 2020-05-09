<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

/**
 * TemporaryActionInterface represents an action, that is generated for
 * runtime-only purposes
 *
 * Actions that implement this interface MUST NOT be saved into the storage
 * and SHOULD be used only for runtime calculations.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface TemporaryActionInterface
{
}
