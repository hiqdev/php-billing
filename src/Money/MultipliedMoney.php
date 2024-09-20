<?php

declare(strict_types=1);

namespace hiqdev\php\billing\Money;

use Laminas\Code\Reflection\Exception\InvalidArgumentException;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\MoneyParser;
use Money\Parser\DecimalMoneyParser;

/**
 * Class MultipliedMoney a wrapper around the Money class and provides
 * a way to work with sub-cent prices.
 *
 * For example, if you have a price of $0.001, it will be represented as
 * 1 USD with a multiplier of 1000. After you do some calculations with this
 * price, you can convert it back to the decimal representation by dividing
 * the amount by the multiplier.
 *
 * By default, the MultipliedMoney uses the DecimalMoneyParser to parse the
 * amount. You can set your own parser by calling the setDecimalMoneyParser.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class MultipliedMoney
{
    private function __construct(
        private readonly Money $money,
        private readonly int $multiplier = 1
    ) {
    }

    /**
     * @param numeric-string $amount
     * @param string $currencyCode
     */
    public static function create(string $amount, string $currencyCode): MultipliedMoney
    {
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('Amount of the MultipliedMoney must be numeric');
        }

        $currency = new Currency($currencyCode);
        $parser = self::getMoneyParser();

        if (!self::isFloat($amount) || self::isWhole($amount)) {
            return new self($parser->parse($amount, $currency), 1);
        }

        $multiplier = self::calculateMultiplierToInteger($amount);
        return new self(
            $parser->parse((string)($amount * $multiplier), $currency),
            $multiplier
        );
    }

    public function money(): Money
    {
        return $this->money;
    }

    public function multiplier(): int
    {
        return $this->multiplier;
    }

    public function getCurrency(): Currency
    {
        return $this->money->getCurrency();
    }

    public function getAmount(): string
    {
        return $this->money->getAmount();
    }

    /**
     * @param numeric-string $amount
     */
    private static function calculateMultiplierToInteger(string $amount): int
    {
        if (self::isWhole($amount)) {
            return 1;
        }

        [, $fraction] = explode('.', $amount, 2);
        return (int)('1' . implode(array_fill(0, strlen($fraction), 0)));
    }

    /**
     * @param numeric-string $number
     */
    private static function isWhole(string $number): bool
    {
        /** @noinspection PhpWrongStringConcatenationInspection */
        return is_int($number + 0);
    }

    /**
     * @param numeric-string $number
     */
    private static function isFloat(string $number): bool
    {
        return str_contains($number, '.');
    }

    private static MoneyParser $moneyParser;
    public static function setDecimalMoneyParser(MoneyParser $moneyParser): void
    {
        self::$moneyParser = $moneyParser;
    }
    private static function getMoneyParser(): MoneyParser
    {
        if (!isset(self::$moneyParser)) {
            self::setDecimalMoneyParser(new DecimalMoneyParser(new ISOCurrencies()));
        }

        return self::$moneyParser;
    }

}
