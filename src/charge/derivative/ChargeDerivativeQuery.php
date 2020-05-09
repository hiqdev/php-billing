<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\derivative;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Class ChargeDerivativeQuery
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class ChargeDerivativeQuery implements ChargeDerivativeQueryInterface
{
    private $changed = [];

    public function get(string $name, $default = null)
    {
        return $this->isChanged($name)
            ? $this->changed[$name]
            : $default;
    }

    public function changeId($id): ChargeDerivativeQueryInterface
    {
        $this->changed['id'] = $id;

        return $this;
    }

    public function changeType(TypeInterface $type): ChargeDerivativeQueryInterface
    {
        $this->changed['type'] = $type;

        return $this;
    }

    public function changeTarget(TargetInterface $target): ChargeDerivativeQueryInterface
    {
        $this->changed['target'] = $target;

        return $this;
    }

    public function changeAction(ActionInterface $action): ChargeDerivativeQueryInterface
    {
        $this->changed['action'] = $action;

        return $this;
    }

    public function changePrice(PriceInterface $price): ChargeDerivativeQueryInterface
    {
        $this->changed['price'] = $price;

        return $this;
    }

    public function changeUsage(QuantityInterface $quantity): ChargeDerivativeQueryInterface
    {
        $this->changed['usage'] = $quantity;

        return $this;
    }

    public function changeSum(Money $sum): ChargeDerivativeQueryInterface
    {
        $this->changed['sum'] = $sum;

        return $this;
    }

    public function changeBill(BillInterface $bill): ChargeDerivativeQueryInterface
    {
        $this->changed['bill'] = $bill;

        return $this;
    }

    public function changeComment(?string $comment): ChargeDerivativeQueryInterface
    {
        $this->changed['comment'] = $comment;

        return $this;
    }

    public function changeParent(?ChargeInterface $charge): ChargeDerivativeQueryInterface
    {
        $this->changed['charge'] = $charge;

        return $this;
    }

    public function isChanged(string $field): bool
    {
        return isset($this->changed[$field]);
    }

    public function getParent(): ?ChargeInterface
    {
        return $this->get('parent');
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getUsage(): ?QuantityInterface
    {
        return $this->get('usage');
    }

    public function getType(): ?TypeInterface
    {
        return $this->get('type');
    }

    public function getComment(): ?string
    {
        return $this->get('comment');
    }

    public function getSum(): ?Money
    {
        return $this->get('sum');
    }

    public function getAction(): ?ActionInterface
    {
        return $this->get('action');
    }

    public function getBill(): ?BillInterface
    {
        return $this->get('bill');
    }

    public function getPrice(): ?PriceInterface
    {
        return $this->get('price');
    }

    public function getTarget(): ?TargetInterface
    {
        return $this->get('target');
    }
}
