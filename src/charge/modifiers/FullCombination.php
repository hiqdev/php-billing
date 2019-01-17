<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\ChargeModifier;

/**
 * Class FullCombination combines charges from all formulas from $left and $right parts of condition
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class FullCombination implements ChargeModifier
{
    /**
     * @var ChargeModifier
     */
    protected $left;
    /**
     * @var ChargeModifier
     */
    protected $right;

    public function __construct(ChargeModifier $left, ChargeModifier $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        $leftCharges = [$charge];
        $rightCharges = [$charge];

        if ($this->left->isSuitable($charge, $action)) {
            $leftCharges = $this->left->modifyCharge($charge, $action);

            if ($charge && empty($leftCharges)) {
                return []; // If there was at least one charge, but it disappeared – modifier does not want this charge to happen. Stop.
            }

            $originalChargeExists = array_reduce($leftCharges, function ($result, Charge $item) use ($charge) {
                return $result || $charge === $item;
            }, false);
            if ($charge && !$originalChargeExists) {
                return $leftCharges;
            }
            // $leftCharge will contain original charge and 0+ additional charges (discounts)
        }

        /** @var Charge $leftTotal */
        /** @var Charge $charge */
        $leftTotal = $this->sumCharges($charge, $leftCharges);
        if ($this->right->isSuitable($leftTotal, $action)) {
            /** @var Charge[] $dirtyRightCharges */
            $dirtyRightCharges = $this->right->modifyCharge($leftTotal, $action);
            if ($leftTotal && empty($dirtyRightCharges)) {
                return []; // If there was a charge, but it disappeared – modifier does not want this charge to happen. Stop.
            }

            $lastLeftCharge = end($leftCharges);
            $rightCharges = array_filter($dirtyRightCharges, function (ChargeInterface $charge) use ($leftTotal, $lastLeftCharge) {
                /** @var Charge $charge */
                if ($charge->getParent() === $leftTotal) {
                    $charge->overwriteParent($lastLeftCharge);
                }

                // Drop $leftTotal from $rightCharges array
                return $charge !== $leftTotal;
            });

            if (\count($rightCharges) === \count($dirtyRightCharges)) { // Original $leftTotal was not returned
                foreach ($rightCharges as $rightCharge) {
                    $rightCharge->overwriteParent($charge);
                }

                return $rightCharges;
            }
        }

        if ($charge && $leftTotal) {
            // If we had both charge and left hand total charge – pass comments and events that were probably generated in left total
            if ($leftTotal->getComment()) {
                $charge->setComment($leftTotal->getComment());
            }

            $events = $leftTotal->releaseEvents();
            if (!empty($events)) {
                foreach ($events as $event) {
                    $charge->recordThat($event);
                }
            }
        }

        return $this->unique(array_merge($leftCharges, $rightCharges));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuitable(?ChargeInterface $charge, ActionInterface $action): bool
    {
        return $this->left->isSuitable($charge, $action) || $this->right->isSuitable($charge, $action);
    }

    /**
     * @param ChargeInterface[] $charges
     * @return ChargeInterface[] unique charges
     */
    private function unique(array $charges): array
    {
        $hashes = [];
        $result = [];

        foreach ($charges as $charge) {
            $hash = spl_object_hash($charge);
            if (isset($hashes[$hash])) {
                continue;
            }
            $hashes[$hash] = true;
            $result[] = $charge;
        }

        return $result;
    }

    /**
     * @param ChargeInterface $originalCharge
     * @param ChargeInterface[] $producedCharges
     * @return ChargeInterface|null
     * @throws \Exception
     */
    private function sumCharges(?ChargeInterface $originalCharge, array $producedCharges): ?ChargeInterface
    {
        if ($originalCharge === null) {
            return null;
        }

        $sum = $originalCharge->getSum();

        $additionalCharges = [];
        foreach ($producedCharges as $charge) {
            if ($originalCharge === $charge) {
                continue;
            }

            $additionalCharges[] = $charge;
            $sum = $sum->add($charge->getSum());
        }

        if (empty($additionalCharges)) {
            return $originalCharge;
        }

        $tempCharge = new Charge(
            $originalCharge->getId(),
            $originalCharge->getType(),
            $originalCharge->getTarget(),
            $originalCharge->getAction(),
            $originalCharge->getPrice(),
            $originalCharge->getUsage(),
            $sum,
            $originalCharge->getBill()
        );
        if ($originalCharge->getComment() !== null) {
            $tempCharge->setComment($originalCharge->getComment());
        }
        if ($originalCharge->getParent() !== null) {
            $tempCharge->setParent($originalCharge->getParent());
        }

        return $tempCharge;
    }
}
