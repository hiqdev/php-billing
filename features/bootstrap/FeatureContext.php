<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Currency;
use Money\Money;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     */
    public function __construct()
    {
        $this->customer = new Customer(null, 'somebody');
    }

    /**
     * @Given /(\S+) (\S+) price is ([0-9.]+) (\w+) per ([0-9.]+) (\w+)/
     */
    public function price($target, $type, $sum, $currency, $amount, $unit)
    {
        $type = new Type(Type::ANY, $type);
        $target = new Target(Target::ANY, $target);
        $quantity = Quantity::create($unit, $amount);
        $sum = new Money($sum, new Currency($currency));
        $this->price = new SinglePrice(null, $type, $target, null, $quantity, $sum);
    }

    /**
     * @Given /action is (\S+) (\w+) ([0-9.]+) (\S+)/
     */
    public function action($target, $type, $amount, $unit)
    {
        $type = new Type(Type::ANY, $type);
        $target = new Target(Target::ANY, $target);
        $quantity = Quantity::create($unit, $amount);
        $time = new DateTimeImmutable();
        $this->action = new Action(null, $type, $target, $quantity, $this->customer, $time);
    }

    /**
     * @Given /formula (.+)/
     */
    public function formula($formula)
    {
        $this->formula = $formula;
    }

    /**
     * @When /date is ([0-9.-]+)/
     */
    public function dateIs($date)
    {
        $this->date = $date;
    }

    /**
     * @Then /first charge is (\S+) ([0-9.]+)? ?([A-Z]{3})?/
     */
    public function firstCharge($type, $sum = null, $currency = null)
    {
        $this->charge(1, $type, $sum, $currency);
    }

    /**
     * @Then /second charge is (\S+) ([0-9.]+)? ?([A-Z]{3})?/
     */
    public function secondCharge($type, $sum = null, $currency = null)
    {
        $this->charge(2, $type, $sum, $currency);
    }

    /**
     * @Then /third charge is (\S+) ([0-9.]+)? ?([A-Z]{3})?/
     */
    public function thirdCharge($type, $sum = null, $currency = null)
    {
        $this->charge(3, $type, $sum, $currency);
    }

    public function charge($no, $type, $sum = null, $curency = null)
    {
        var_dump($this->formula);
        var_dump($this->date);
        var_dump($type);
        var_dump($sum);
        var_dump($currency);
    }
}
