<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\behat\bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Closure;
use DateTimeImmutable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\Generalizer;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\formula\FormulaEngine;
use hiqdev\php\billing\order\Calculator;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Currencies\ISOCurrencies;
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
    /** @var Generalizer */
    protected $generalizer;
    /** @var Calculator */
    protected $calculator;

    /**
     * Initializes context.
     */
    public function __construct()
    {
        $this->customer = new Customer(null, 'somebody');
        $this->moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $this->generalizer = new Generalizer();
        $this->calculator = new Calculator($this->generalizer, null, null);
    }

    /**
     * @Given /(\S+) (\S+) price is ([0-9.]+) (\w+) per (\w+)/
     */
    public function priceIs($target, $type, $sum, $currency, $unit)
    {
        $type = new Type(Type::ANY, $type);
        $target = new Target(Target::ANY, $target);
        $quantity = Quantity::create($unit, 0);
        $sum = $this->moneyParser->parse($sum, $currency);
        $this->price = new SinglePrice(null, $type, $target, null, $quantity, $sum);
    }

    /**
     * @Given /action is (\S+) (\w+) ([0-9.]+) (\S+)/
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
     * @param string $formula
     */
    public function formulaIs(string $formula): void
    {
        $this->formula = $formula;
    }

    /**
     * @Given /formula continues (.+)/
     * @param string $formula
     */
    public function formulaContinues(string $formula): void
    {
        $this->formula .= "\n" . $formula;
    }

    protected function getFormulaEngine()
    {
        if ($this->engine === null) {
            $this->engine = new FormulaEngine();
        }

        return $this->engine;
    }

    /**
     * @When /action date is ([0-9.-]+)/
     * @param string $date
     * @throws \Exception
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
     * @param string $numeral
     */
    public function emptyCharge(string $numeral): void
    {
        $this->chargeIs($numeral);
    }

    /**
     * @Then /^(\w+) charge is (\S+) +(-?[0-9.]+) ([A-Z]{3})(?: with (.+)?)?$/
     */
    public function chargeWithSum($numeral, $type = null, $sum = null, $currency = null, $events = null): void
    {
        $this->chargeIs($numeral, $type, $sum, $currency, null, $events);
    }

    /**
     * @Then /^(\w+) charge is (\S+) +(-?[0-9.]+) ([A-Z]{3}) reason ([\w]+)(?: with (.+)?)?$/
     */
    public function chargeWithReason($numeral, $type = null, $sum = null, $currency = null, $reason = null, $events = null): void
    {
        $this->chargeIs($numeral, $type, $sum, $currency, $reason, $events);
    }

    public function chargeIs($numeral, $type = null, $sum = null, $currency = null, $reason = null, $events = null): void
    {
        $no = $this->ensureNo($numeral);
        if ($no === 0) {
            $this->calculatePrice();
        }
        $this->assertCharge($this->charges[$no] ?? null, $type, $sum, $currency, $reason, $events);
    }

    /**
     * @When /^calculating charges$/
     */
    public function calculatePrice(): void
    {
        $this->expectError(function () {
            $this->price->setModifier($this->getFormulaEngine()->build($this->formula));
            $this->charges = $this->calculator->calculatePrice($this->price, $this->action);
        });
    }

    public function expectError(Closure $closure): void
    {
        try {
            call_user_func($closure);
        } catch (\Exception $e) {
            if ($this->isExpectedError($e)) {
                $this->expectedError = null;
            } else {
                throw $e;
            }
        }
        if ($this->expectedError) {
            throw new \Exception('failed receive expected exception');
        }
    }

    protected function isExpectedError(\Exception $e): bool
    {
        return $this->startsWith($e->getMessage(), $this->expectedError);
    }

    protected function startsWith(string $string, string $prefix = null): bool
    {
        return $prefix && strncmp($string, $prefix, strlen($prefix)) === 0;
    }

    /**
     * @param ChargeInterface|Charge|null $charge
     * @param string|null $type
     * @param string|null $sum
     * @param string|null $currency
     * @param string|null $reason
     * @param string|null $events
     */
    public function assertCharge($charge, $type, $sum, $currency, $reason, $events): void
    {
        if (empty($type) && empty($sum) && empty($currency)) {
            Assert::assertNull($charge);

            return;
        }
        Assert::assertInstanceOf(Charge::class, $charge);
        Assert::assertSame($type, $this->normalizeType($charge->getType()->getName()));
        $money = $this->moneyParser->parse($sum, $currency);
        Assert::assertTrue($money->equals($charge->getSum()), sprintf(
            'Charge sum %s does not match expected %s', $charge->getSum()->getAmount(), $money->getAmount()
        ));
        if ($reason !== null) {
            Assert::assertSame($reason, $charge->getComment(),
                sprintf('Charge comment %s does not match expected %s', $charge->getComment(), $reason)
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
        return $string === 'discount,discount' ? 'discount' : $string;
    }

    private function ensureNo(string $numeral): int
    {
        $formatter = new NumberFormatter('en_EN', NumberFormatter::SPELLOUT);
        $result = $formatter->parse($numeral);
        if ($result === false) {
            throw new \Exception("Wrong numeral '$numeral'");
        }

        return --$result;
    }
}
