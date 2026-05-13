<?php

declare(strict_types=1);

/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */
namespace hiqdev\php\billing\customer;

/**
 * Customer.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Customer implements CustomerInterface
{
    /**
     * @var CustomerInterface
     */
    protected $seller;

    /**
     * @var Customer[]
     */
    protected $sellers = [];

    /**
     * @param int $id
     * @param string $login
     */
    public function __construct(
        protected $id,
        protected $login,
        ?CustomerInterface $seller = null
    ) {
        $this->seller = $seller;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUniqueId()
    {
        return $this->getId() ?: $this->getLogin();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeller()
    {
        return $this->seller;
    }

    public static function fromArray(array $info)
    {
        $seller = !empty($info['seller_id']) && !empty($info['seller'])
            ? new static($info['seller_id'], $info['seller'])
            : null;

        return new static($info['id'], $info['login'], $seller);
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
