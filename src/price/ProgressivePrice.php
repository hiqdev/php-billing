<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

class ProgressivePrice extends AbstractPrice
{

    public const SIGN_GREATER = '>';
    public const SIGN_LESS = '<';
    public const SIGN_GREATER_EQUAL = '>=';
    public const SIGN_LESS_EQUAL = '<=';
    public const SIGN_EQUAL = '=';

    /* @psalm-var array{array{
     * "price": numeric,
     * "currency": string,
     * "sign_from": string,
     * "value_from": numeric,
     * "sign_till": string,
     * "value_till", numeric
     * }} $condition
     */
    protected array $condition;
    /**
     * @var QuantityInterface prepaid quantity also implies Unit
     * XXX cannot be null cause Unit is required
     */
    protected $prepaid;

    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        QuantityInterface $prepaid,
        array $condition,
        PlanInterface $plan = null,
    ) {
        parent::__construct($id, $type, $target, $plan);
        $this->prepaid = $prepaid;
        $this->condition = $condition;
    }

    public function getPrepaid()
    {
        return $this->prepaid;
    }

    public function getCondition(): array
    {
        return $this->condition;
    }

    public function setCondition(string $key, array $condition): self
    {
        $this->condition[] = $condition;
        return $this;
    }

    private function prepareCondition(): void
    {
        uksort($this->condition, function($a, $b)
        {
            return $b['value_till'] - $a['value_till'];
        }
        );
    }

    /**
     * @inheritDoc
     */
    public function calculateUsage(QuantityInterface $quantity): ?QuantityInterface
    {
        $usage = $quantity->convert($this->prepaid->getUnit())->subtract($this->prepaid);

        if ($usage->isPositive()) {
            return $usage;
        }

        return Quantity::create($this->prepaid->getUnit()->getName(), 0);
    }

    /**
     * @inheritDoc
     */
    public function calculatePrice(QuantityInterface $quantity): ?Money
    {
        $result = null;
        $this->prepareCondition();
        $usage = $this->calculateUsage($quantity);
        $quantity = $usage->getQuantity();
        foreach ($this->condition as $condition) {
            $quantity = $usage->getQuantity();
            if (isset($condition['sign_from'])) {
                if ($condition['sign_till'] === self::SIGN_EQUAL) {
                    $result = $usage->getQuantity();
                }
            } else {
                if ($condition['sign_till'] === self::SIGN_EQUAL || $condition['sign_till'] === self::SIGN_GREATER) {

                }
            }
        }
        return $result;
    }

    private function getIntervalBoundary(string $sign, int $value): float
    {
        switch ($sign) {
            case self::SIGN_GREATER:
                return $value + 0.01;
            case self::SIGN_LESS:
                return $value - 0.01;
            default:
                return $value;
        }
    }
}
