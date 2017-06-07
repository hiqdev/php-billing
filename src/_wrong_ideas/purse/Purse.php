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
 * Purse.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Purse implements PurseInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var PurseInterface
     */
    protected $customer;

    public function __construct($id, $currency, $customer)
    {
        $this->id = $id;
        $this->currency = $currency;
        $this->customer = $customer;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
