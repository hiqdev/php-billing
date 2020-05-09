<?php

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
 * Interface ChargeDerivativeQueryInterface represents a query to build
 * a new charge with [[ChargeDerivativeInterface]], but with some changes
 * of properties.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface ChargeDerivativeQueryInterface
{
    public function changeId($id): self;

    public function changeType(TypeInterface $type): self;

    public function changeTarget(TargetInterface $target): self;

    public function changeAction(ActionInterface $action): self;

    public function changePrice(PriceInterface $price): self;

    public function changeUsage(QuantityInterface $quantity): self;

    public function changeSum(Money $sum): self;

    public function changeBill(BillInterface $bill): self;

    public function changeComment(?string $comment): self;

    public function changeParent(?ChargeInterface $charge): self;

    public function getParent(): ?ChargeInterface;

    /**
     * @return string|int|null
     */
    public function getId();

    public function getUsage(): ?QuantityInterface;

    public function getType(): ?TypeInterface;

    public function getTarget(): ?TargetInterface;

    public function getComment(): ?string;

    public function getSum(): ?Money;

    public function getAction(): ?ActionInterface;

    public function getBill(): ?BillInterface;

    public function getPrice(): ?PriceInterface;

    public function isChanged(string $field): bool;
}
