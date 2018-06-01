<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\addons;

use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\modifiers\AddonInterface;
use hiqdev\php\billing\formula\FormulaSemanticsError;
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
        return $this->value;
    }

    public function isAbsolute()
    {
        return $this->value instanceof Money;
    }

    public function isRelative()
    {
        return !$this->isAbsolute();
    }

    public function ensureValidValue($value)
    {
        if ($value instanceof static) {
            return $value->getValue();
        }

        if ($value instanceof Money) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_string($value) && preg_match('/^(\d{1,5}(\.\d+)?)%$/', $value, $ms)) {
            return $ms[1];
        }

        if (is_string($value) && preg_match('/^(\d{1,5}(\.\d+)?) ([A-Z]{3})$/', $value, $ms)) {
            return $this->moneyParser->parse($ms[1], new Currency($ms[3]));
        }

        /// var_dump($value);

        /// TODO: add special exception
        $name = static::$name;
        throw new FormulaSemanticsError("invalid $name value: $value");
    }

    public function multiply($multiplier)
    {
        if (!is_numeric($multiplier)) {
            throw new FormulaSemanticsError('multiplier for discount must be numeric');
        }

        return new static($this->isAbsolute() ? $this->value->multiply($multiplier) : $this->value*$multiplier);
    }

    public function add($addend)
    {
        if (!$addend instanceof self) {
            $addend = new self($addend);
        }
        $this->ensureSameType($addend, 'addend');

        if ($this->isAbsolute()) {
            $sum = $this->value->add($addend->getValue());
        } else {
            $sum = $this->value + $addend->getValue();
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
        return $this->isAbsolute() ? $this->value : $charge->getSum()->multiply($this->value*0.01);
    }
}
