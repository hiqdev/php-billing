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

    protected ?CustomerState $state = null;

    public function __construct($id, $login, CustomerInterface $seller = null, ?CustomerState $state = null)
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
    public function getState(): ?CustomerState
    {
        return $this->state;
    }

    public function setState(CustomerState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function isDeleted(): bool
    {
        return CustomerState::isDeleted($this);
    }

    public static function fromArray(array $info)
    {
        if (!empty($info['seller_id']) && !empty($info['seller'])) {
            $seller = new static($info['seller_id'], $info['seller']);
        } else {
            $seller = null;
        }

        return new static(
            $info['id'],
            $info['login'],
            $seller,
            isset($info['state']) ? CustomerState::from($info['state']) : null,
        );
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
