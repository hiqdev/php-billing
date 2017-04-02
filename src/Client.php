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
class Client extends AbstractTarget implements ClientInterface
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
     * @var ClientInterface
     */
    public $seller;

    /**
     * @var Client[]
     */
    public $sellers = [];

    public function __construct($id, $login = null)
    {
        parent::__construct('client', $id);
        $this->login = $login;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogin()
    {
        return $this->login;
    }
}
