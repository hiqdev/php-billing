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
 * Client.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Client
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $login;

    /**
     * @var Client
     */
    public $seller;

    /**
     * @var Client[]
     */
    public $sellers = [];
}
