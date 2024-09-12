<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\behat\bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Cache\Adapter\PHPArray\ArrayCachePool;
use Closure;
use DateTimeImmutable;
use Exception;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\formula\FormulaEngine;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\price\MoneyBuilder;
use hiqdev\php\billing\price\PriceHelper;
use hiqdev\php\billing\price\ProgressivePrice;
use hiqdev\php\billing\price\ProgressivePriceThreshold;
use hiqdev\php\billing\price\ProgressivePriceThresholdList;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\tests\support\order\SimpleBilling;
use hiqdev\php\billing\type\AnyIdType;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use NumberFormatter;
use PHPUnit\Framework\Assert;
use ReflectionClass;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected $engine;

    /** @var Customer */
    protected $customer;
    /**
     * @var \hiqdev\php\billing\price\PriceInterface|\hiqdev\php\billing\charge\FormulaChargeModifierTrait
     *
     * TODO: FormulaChargeModifierTrait::setFormula() must be moved to interface
     */
    protected $price;

    /** @var string */
    protected $formula;

    /**
     * @var \hiqdev\php\billing\action\ActionInterface|\hiqdev\php\billing\action\AbstractAction
     */
    protected $action;
    /**
     * @var ChargeInterface[]
     */
    protected $charges;

    /** @var \Money\MoneyParser */
    protected $moneyParser;

    /** @var string */
    protected $expectedError;

    /**
     * Initializes context.
     */
    public function __construct()
    {
        error_reporting(E_ALL & ~E_DEPRECATED);
        date_default_timezone_set('UTC');
        $this->customer = new Customer(null, 'somebody');
        $this->moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $this->plan = new Plan(null, 'plan', $this->customer);
        $this->sale = new Sale(null, Target::any(), $this->customer, $this->plan, new DateTimeImmutable('2000-01-01'));
        $this->billing = SimpleBilling::fromSale($this->sale);
    }

    /**
     * @Given /(\S+) (\S+) price is ([0-9.]+) (\w+) per (\w+)(?: includes ([\d.]+))?/
     */
    public function priceIs($target, $type, $sum, $currency, $unit, $quantity = 0)
    {
        $type = new AnyIdType($type);
        $target = new Target(Target::ANY, $target);
        $quantity = Quantity::create($unit, $quantity);
        $sum = $this->moneyParser->parse($sum, new Currency($currency));
        $this->setPrice(new SinglePrice(null, $type, $target, null, $quantity, $sum));
    }

    protected array $progressivePrice = [];
    /**
     * @Given /(\S+) progressive price for (\S+) is +(\S+) (\S+) per (\S+) (\S+) (\S+) (\S+)$/
     */
    public function progressivePrice($target, $type, $price, $currency, $unit, $sign, $quantity, $perUnit): void
    {
        if (empty($this->progressivePrice[$type])) {
            $this->progressivePrice[$type] = [
                'target' => $target,
                'price' => $price,
                'currency' => $currency,
                'prepaid' => $quantity,
                'unit' => $unit,
                'thresholds' => [],
            ];
        } else {
            $this->progressivePrice[$type]['thresholds'][] = [
                'price' => $price,
                'currency' => $currency,
                'unit' => $unit,
                'quantity' => $quantity,
            ];
        }
    }

    /**
     * @Given /^build progressive price/
     */
    public function buildProgressivePrices()
    {
        $i = 0;
        foreach ($this->progressivePrice as $type => $price) {
            $type = new AnyIdType($type);
            $target = new Target(Target::ANY, $price['target']);
            $quantity = Quantity::create($price['unit'], $price['prepaid']);
            if ($i++ === 0) {
                $price['price'] *= 100;
            }
            $money = new Money($price['price'], new Currency($price['currency']));
            $thresholds = ProgressivePriceThresholdList::fromScalarsArray($price['thresholds']);
            $price = new ProgressivePrice(null, $type, $target, $quantity, $money, $thresholds);
            $this->setPrice($price);
        }
    }

    /**
     * @Given /sale close time is ([0-9.-]+)?/
     */
    public function setActionCloseTime($closeTime): void
    {
        if ($closeTime === null) {
            return;
        }

        $this->sale->close(new DateTimeImmutable($closeTime));
    }

    /**
     * @Given /sale time is (.+)$/
     */
    public function setSaleTime($time): void
    {
        $ref = new ReflectionClass($this->sale);
        $prop = $ref->getProperty('time');
        $prop->setAccessible(true);
        $prop->setValue($this->sale, new DateTimeImmutable($time));
        $prop->setAccessible(false);
    }

    private function setPrice($price)
    {
        $this->price = $price;
        $ref = new ReflectionClass($this->plan);
        $prop = $ref->getProperty('prices');
        $prop->setAccessible(true);
        $prop->setValue($this->plan, [$price]);
    }

    /**
     * @Given /action is (\S+) ([\w_,]+)(?: ([0-9.]+) (\S+))?(?: in (.+))?/
     */
    public function actionIs(string $target, string $type, float $amount, string $unit, ?string $date = null): void
    {
        $type = new AnyIdType($type);
        $target = new Target(Target::ANY, $target);
        $time = new DateTimeImmutable($date);
        if ($this->sale->getCloseTime() instanceof DateTimeImmutable) {
            $amount = $amount * $this->getFractionOfMonth(
                $time, $time, $this->sale->getCloseTime()
            );
        }
        $quantity = Quantity::create($unit, $amount);

        $this->action = new Action(null, $type, $target, $quantity, $this->customer, $time);
    }

    private function getFractionOfMonth(DateTimeImmutable $month, DateTimeImmutable $startTime, DateTimeImmutable $endTime): float
    {
        // SQL function: days2quantity()

        $month = $month->modify('first day of this month 00:00');
        if ($startTime < $month) {
            $startTime = $month;
        }
        if ($endTime > $month->modify('first day of next month 00:00')) {
            $endTime = $month->modify('first day of next month 00:00');
        }

        $secondsInMonth = $month->format('t') * 24 * 60 * 60;

        return ($endTime->getTimestamp() - $startTime->getTimestamp()) / $secondsInMonth;
    }

    /**
     * @Given /formula is (.+)/
     */
    public function formulaIs(string $formula): void
    {
        $this->formula = $formula;
    }

    /**
     * @Given /formula continues (.+)/
     */
    public function formulaContinues(string $formula): void
    {
        $this->formula .= "\n" . $formula;
    }

    protected function getFormulaEngine()
    {
        if ($this->engine === null) {
            $this->engine = new FormulaEngine(new ArrayCachePool());
        }

        return $this->engine;
    }

    /**
     * @When /action date is (.+)/
     * @throws Exception
     */
    public function actionDateIs(string $date): void
    {
        $this->action->setTime(new DateTimeImmutable($date));
    }

    /**
     * @Given /^client rejected service at (.+)$/
     */
    public function actionCloseDateIs(string $close_date): void
    {
        $this->sale->close(new DateTimeImmutable($close_date));
    }

    /**
     * @Then /^error is$/m
     */
    public function multilineErrorIs(\Behat\Gherkin\Node\PyStringNode $value)
    {
        $this->expectedError = $value->getRaw();
    }

    /**
     * @Then /^error is (.+)$/
     *
     * @param string $error
     */
    public function errorIs($error): void
    {
        $this->expectedError = $error;
    }

    /**
     * @Then /^(\w+) charge is ?(?: with ?)?$/
     */
    public function emptyCharge(string $numeral): void
    {
        $this->chargeIs($numeral);
    }

    /**
     * @Then /^(\w+) charge is (\S+) +(-?[0-9.]+) ([A-Z]{3})(?: for ([\d.]+)? (\w+)?)?(?: with (.+)?)?$/
     */
    public function chargeWithSum($numeral, $type = null, $sum = null, $currency = null, $qty = null, $unit = null, $events = null): void
    {
        $this->chargeIs($numeral, $type, $sum, $currency, null, $qty, $unit, $events);
    }

    /**
     * @Then /^(\w+) charge is (\S+) +(-?[0-9.]+) ([A-Z]{3}) reason ([\w]+)(?: with (.+)?)?$/
     */
    public function chargeWithReason($numeral, $type = null, $sum = null, $currency = null, $reason = null, $events = null): void
    {
        $this->chargeIs($numeral, $type, $sum, $currency, $reason, null, null, $events);
    }

    public function chargeIs($numeral, $type = null, $sum = null, $currency = null, $reason = null, $qty = null, $unit = null, $events = null): void
    {
        $no = $this->ensureNo($numeral);
        if ($no === 0) {
            $this->calculatePrice();
        }
        $this->assertCharge($this->charges[$no] ?? null, $type, $sum, $currency, $reason, $qty, $unit, $events);
    }

    /**
     * @When /^calculating charges$/
     */
    public function calculatePrice(): void
    {
        $this->expectError(function () {
            if ($this->formula !== null) {
                $this->price->setModifier($this->getFormulaEngine()->build($this->formula));
            }
            $this->charges = $this->billing->calculateCharges($this->action);
        });
    }

    public function expectError(Closure $closure): void
    {
        try {
            call_user_func($closure);
        } catch (Exception $e) {
            if ($this->isExpectedError($e)) {
                $this->expectedError = null;
            } else {
                throw $e;
            }
        }
        if ($this->expectedError) {
            throw new Exception('failed receive expected exception');
        }
    }

    protected function isExpectedError(Exception $e): bool
    {
        return str_starts_with($e->getMessage(), $this->expectedError);
    }

    /**
     * @param ChargeInterface|Charge|null $charge
     * @param string|null $type
     * @param string|null $sum
     * @param string|null $currency
     * @param string|null $reason
     * @param string|null $qty
     * @param string|null $unit
     * @param string|null $events
     */
    public function assertCharge($charge, $type, $sum, $currency, $reason, $qty, $unit, $events): void
    {
        if (empty($type) && empty($sum) && empty($currency)) {
            is_null($charge) || Assert::assertSame('0', $charge->getSum()->getAmount());
            return;
        }
        Assert::assertInstanceOf(Charge::class, $charge);
        Assert::assertSame($type, $this->normalizeType($charge->getType()->getName()), sprintf(
            'Charge type %s does not match expected %s', $type, $this->normalizeType($charge->getType()->getName())
        ));
        $money = $this->moneyParser->parse($sum, new Currency($currency));
        Assert::assertTrue($money->equals($charge->getSum()), sprintf(
            'Charge sum %s does not match expected %s', $charge->getSum()->getAmount(), $money->getAmount()
        ));
        if ($reason !== null) {
            Assert::assertSame($reason, $charge->getComment(),
                sprintf('Charge comment %s does not match expected %s', $charge->getComment(), $reason)
            );
        }
        if ($qty !== null && $unit !== null) {
            Assert::assertEqualsWithDelta($qty, $charge->getUsage()->getQuantity(), 1e-7,
                sprintf('Charge quantity "%s" does not match expected "%s"', $charge->getUsage()->getQuantity(), $qty)
            );
            Assert::assertSame($unit, $charge->getUsage()->getUnit()->getName(),
                sprintf('Charge unit "%s" does not match expected "%s"', $charge->getUsage()->getUnit()->getName(), $unit)
            );
        }
        if ($events !== null) {
            $storedEvents = $charge->releaseEvents();
            foreach (array_map('trim', explode(',', $events)) as $eventClass) {
                foreach ($storedEvents as $storedEvent) {
                    $eventReflection = new \ReflectionObject($storedEvent);
                    if ($eventReflection->getShortName() === $eventClass) {
                        continue 2;
                    }
                }

                Assert::fail(sprintf('Event of class %s is not present is charge', $eventClass));
            }
        } else {
            Assert::assertEmpty($charge->releaseEvents(), 'Failed asserting that charge does not have events');
        }
    }

    private function normalizeType($string): string
    {
        switch ($string) {
            case 'discount,discount':
                return 'discount';
            case 'monthly,leasing':
                return 'leasing';
            case 'monthly,installment':
                return 'installment';
            default:
                return $string;
        }
    }

    private function ensureNo(string $numeral): int
    {
        $formatter = new NumberFormatter('en_EN', NumberFormatter::SPELLOUT);
        $result = $formatter->parse($numeral);
        if ($result === false) {
            throw new Exception("Wrong numeral '$numeral'");
        }

        return --$result;
    }

    /**
     * @Given /^progressive price calculation steps are (.*)$/
     */
    public function progressivePriceCalculationStepsAre($explanation)
    {
        if (!$this->price instanceof ProgressivePrice) {
            throw new Exception('Price is not progressive');
        }

        $traces = array_map(fn($trace) => $trace->toShortString(), $this->price->getCalculationTraces());
        $billed = implode(' + ', $traces);
        Assert::assertSame($explanation, $billed, 'Progressive price calculation steps mismatch. Expected: ' . $explanation . ', got: ' . $billed);
    }
}
