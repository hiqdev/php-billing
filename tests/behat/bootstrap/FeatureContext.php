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
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\tests\support\order\SimpleBilling;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;
use NumberFormatter;
use PHPUnit\Framework\Assert;

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
        $type = new Type(Type::ANY, $type);
        $target = new Target(Target::ANY, $target);
        $quantity = Quantity::create($unit, $quantity);
        $sum = $this->moneyParser->parse($sum, new Currency($currency));
        $this->setPrice(new SinglePrice(null, $type, $target, null, $quantity, $sum));
    }

    /**
     * @Given /sale close time is ([0-9.-]+)/
     */
    public function setActionCloseTime($closeTime): void
    {
        $this->sale->close(new DateTimeImmutable($closeTime));
    }

    private function setPrice($price)
    {
        $this->price = $price;
        $ref = new \ReflectionClass($this->plan);
        $prop = $ref->getProperty('prices');
        $prop->setAccessible(true);
        $prop->setValue($this->plan, [$price]);
    }

    /**
     * @Given /action is (\S+) ([\w_,]+) ([0-9.]+) (\S+)/
     */
    public function actionIs($target, $type, $amount, $unit)
    {
        $type = new Type(Type::ANY, $type);
        $target = new Target(Target::ANY, $target);
        $quantity = Quantity::create($unit, $amount);
        $time = new DateTimeImmutable();
        $this->action = new Action(null, $type, $target, $quantity, $this->customer, $time);
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
     * @When /action date is ([0-9.-]+)/
     * @throws Exception
     */
    public function actionDateIs(string $date): void
    {
        $this->action->setTime(new DateTimeImmutable($date));
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
            $this->price->setModifier($this->getFormulaEngine()->build($this->formula));
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
}
