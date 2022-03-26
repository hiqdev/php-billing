<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\addons;

use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\modifiers\AddonInterface;
use hiqdev\php\billing\charge\modifiers\PercentPoint;
use hiqdev\php\billing\formula\FormulaSemanticsError;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

/**
 * Discount addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Discount implements AddonInterface
{
    protected static $name = 'discount';

    /**
     * @var string|Money
     */
    protected $value;

    protected $moneyParser;

    public function __construct($value)
    {
        $this->moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $this->value = $this->ensureValidValue($value);
    }

    public function getValue()
    {
        return $this->value instanceof PercentPoint ? $this->value->getNumber() : $this->value;
    }

    public function isAbsolute()
    {
        return $this->value instanceof Money;
    }

    public function isRelative()
    {
        return !$this->isAbsolute();
    }

    public function isPercentPoint(): bool
    {
        return $this->value instanceof PercentPoint;
    }

    public function ensureValidValue($value)
    {
        if ($value instanceof self) {
            return $value->getValue();
        }

        if ($value instanceof Money || $value instanceof PercentPoint || $value instanceof Quantity) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        $name = static::$name;
        if (\is_string($value)) {
            if (preg_match('/^(\d{1,5}(\.\d+)?)(%|pp| [A-Z]{3})$/', $value, $ms)) {
                if ($ms[3] === '%') {
                    return $ms[1];
                }
                if ($ms[3] === 'pp') {
                    return new PercentPoint($ms[1]);
                }

                return $this->moneyParser->parse($ms[1], new Currency(trim($ms[3])));
            }

            if (preg_match('/^(\d{1,5}(\.\d+)?) ([a-z]{2,})$/', $value, $ms)) {
                try {
                    return Quantity::create($ms[3], $ms[1]);
                } catch (\Exception $e) {
                    throw new FormulaSemanticsError("invalid $name value: $value");
                }
            }
        }

        throw new FormulaSemanticsError("invalid $name value: $value");
    }

    public function multiply($multiplier)
    {
        if (!is_numeric($multiplier)) {
            throw new FormulaSemanticsError('multiplier for discount must be numeric');
        }

        return new static($this->isAbsolute() ? $this->value->multiply($multiplier) : $this->getValue()*$multiplier);
    }

    public function add($addend)
    {
        if (!$addend instanceof self) {
            $addend = new self($addend);
        }
        $this->ensureSameType($addend, 'addend');

        if ($this->isAbsolute()) {
            $sum = $this->getValue()->add($addend->getValue());
        } else {
            $sum = $this->getValue() + $addend->getValue();
        }

        return new static($sum);
    }

    public function compare($other)
    {
        if (!$other instanceof self) {
            $other = new self($other);
        }
        $this->ensureSameType($other, 'comparison argument');

        if ($this->isAbsolute()) {
            return $this->value->compare($other->getValue());
        } else {
            return $this->value - $other->getValue();
        }
    }

    public function ensureSameType(self $other, $name)
    {
        if ($this->isRelative() && !$other->isRelative()) {
            throw new FormulaSemanticsError("$name must be relative");
        }
        if ($this->isAbsolute() && !$other->isAbsolute()) {
            throw new FormulaSemanticsError("$name must be absolute");
        }
    }

    public function calculateSum(ChargeInterface $charge): Money
    {
        if ($this->value instanceof Money) {
            return $this->value->multiply($charge->getUsage()->getQuantity());
        }

        if ($this->value instanceof Quantity) {
            $price = $charge->getPrice();
            if (!$price instanceof SinglePrice) {
                throw new \InvalidArgumentException('Discount based on quantity supports only single prices');
            }

            $compensatedQuantity = $this->calculateCompensatedQuantity($charge)->add($price->getPrepaid());

            return $price->calculateSum($compensatedQuantity);
        }

        return $charge->getSum()->multiply(sprintf('%.14F', $this->value * 0.01));
    }

    private function calculateCompensatedQuantity(ChargeInterface $charge): QuantityInterface
    {
        $usage = $charge->getUsage();
        $maxCompensatedAmount = $this->getValue();

        switch ($usage->compare($maxCompensatedAmount)) {
            case -1:
                return $usage;
            case 0:
            case 1:
                return $maxCompensatedAmount;
        }
    }
}
