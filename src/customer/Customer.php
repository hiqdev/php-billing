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

/**
 * Customer.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Customer implements CustomerInterface
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
     * @var Customer[]
     */
    protected $sellers = [];

    /**
     * @var string|null
     */
    protected ?string $state;

    public function __construct($id, $login, CustomerInterface $seller = null, ?string $state = null)
    {
        $this->id = $id;
        $this->login = $login;
        $this->seller = $seller;
        $this->state = $state;
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

    /**
     * {@inheritdoc}
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    public function isDeleted(): bool
    {
        return $this->state === 'deleted';
    }

    public static function fromArray(array $info)
    {
        if (!empty($info['seller_id']) && !empty($info['seller'])) {
            $seller = new static($info['seller_id'], $info['seller']);
        } else {
            $seller = null;
        }

        return new static($info['id'], $info['login'], $seller, $info['state'] ?? null);
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
