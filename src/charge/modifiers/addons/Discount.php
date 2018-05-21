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

use hiqdev\php\billing\charge\modifiers\AddonInterface;
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

        /// TODO: add special exception
        var_dump($value);
        $name = static::$name;
        throw new \Exception("invalid $name value: $value");
    }

    public function multiply($multiplier)
    {
        if (!is_numeric($multiplier)) {
            throw new \Exception('multiplier for discount myst be numeric');
        }

        return new static($this->isAbsolute() ? $this->value->multiply($multiplier) : $this->value*$multiplier);
    }

    public function add($addend)
    {
        if ($this->isRelative() && !is_numeric($addend)) {
            throw new \Exception('addend for discount must be numeric because discount is relative');
        }
        if ($this->isAbsolute() && !$addend instanceof Money) {
            throw new \Exception('addend for discount must be money because discount is absolute');
        }

        return new static($this->isAbsolute() ? $this->value->add($addend) : $this->value+$addend);
    }
}
