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
 * Customer.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Customer extends AbstractTarget implements CustomerInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $login;

    /**
     * @var CustomerInterface
     */
    protected $seller;

    /**
     * @var Client[]
     */
    protected $sellers = [];

    public function __construct($id, $login, $seller = null)
    {
        $this->id = $id;
        $this->login = $login;
        $this->seller = $seller;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogin()
    {
        return $this->login;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
