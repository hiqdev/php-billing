<?php

namespace hiqdev\php\billing\charge\derivative;

use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;

/**
 * Class ChargeDerivative
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class ChargeDerivative implements ChargeDerivativeInterface
{
    public function __invoke(ChargeInterface $originalCharge, ChargeDerivativeQueryInterface $query): ChargeInterface
    {
        $tempCharge = new Charge(
            $query->get('id', $originalCharge->getId()),
            $query->get('type', $originalCharge->getType()),
            $query->get('target', $originalCharge->getTarget()),
            $query->get('action', $originalCharge->getAction()),
            $query->get('price', $originalCharge->getPrice()),
            $query->get('usage', $originalCharge->getUsage()),
            $query->get('sum', $originalCharge->getSum()),
            $query->get('bill', $originalCharge->getBill())
        );

        if ($query->get('comment', $originalCharge->getComment()) !== null) {
            $tempCharge->setComment($query->get('comment', $originalCharge->getComment()));
        }
        if ($query->get('parent', $originalCharge->getParent()) !== null) {
            $tempCharge->setParent($query->get('parent', $originalCharge->getParent()));
        }

        return $tempCharge;
    }
}
