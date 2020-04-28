<?php

namespace hiqdev\php\billing\tests\behat\bootstrap;

use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use PHPUnit\Framework\Assert;

class BillingContext extends BaseContext
{
    protected $bill;

    protected array $charges = [];

    /**
     * @Given reseller :reseller
     */
    public function reseller($reseller)
    {
        $this->builder->buildReseller($reseller);
    }

    /**
     * @Given customer :customer
     */
    public function customer($customer)
    {
        $this->builder->buildCustomer($customer);
    }

    /**
     * @Given manager :manager
     */
    public function manager($manager)
    {
        $this->builder->buildManager($manager);
    }

    /**
     * @Given /^(grouping )?(\S+) tariff plan (\S+)/
     */
    public function plan($grouping, $type, $plan)
    {
        $this->builder->buildPlan($plan, $type, !empty($grouping));
    }

    protected function fullPrice(array $data)
    {
        $this->builder->buildPrice($data);
    }

    /**
     * @Given /price for (\S+) is +(\S+) (\S+) per (\S+) for target (\S+)/
     */
    public function priceWithObject($type, $price, $currency, $unit, $target)
    {
        return $this->fullPrice(compact('type', 'price', 'currency', 'unit', 'target'));
    }

    /**
     * @Given /price for (\S+) is +(\S+) (\S+) per (\S+) prepaid (\S+)/
     */
    public function priceWithOver($type, $price, $currency, $unit, $prepaid)
    {
        return $this->fullPrice(compact('type', 'price', 'currency', 'unit', 'prepaid'));
    }

    /**
     * @Given /price for (\S+) is +(\S+) (\S+) per (\S+)$/
     */
    public function price($type, $price, $currency, $unit)
    {
        return $this->fullPrice(compact('type', 'price', 'currency', 'unit'));
    }

    /**
     * @Given /price for (\S+) is +(\S+) (\S+) per 1 (\S+) and (\S+) (\S+) per 2 (\S+) for target (\S+)/
     */
    public function enumPrice($type, $price, $currency, $unit, $price2, $currency2, $unit2, $target)
    {
        $sums = [1 => $price, 2 => $price2];
        return $this->fullPrice(compact('type', 'sums', 'currency', 'unit', 'target'));
    }

    /**
     * @Given /^remove and recreate tariff plan (\S+)/
     */
    public function recreatePlan($plan)
    {
        $this->builder->recreatePlan($plan);
    }

    /**
     * @Given /sale (\S+) for (\S+) plan:(\S+) time:(\S+)/
     */
    public function sale($id, $target, $plan, $time): void
    {
        $this->builder->buildSale($id, $target, $plan, $time);
    }

    /**
     * @Given /resource consumption for (\S+) is (\d+) (\S+) (\S+) for target (\S+)/
     */
    public function setConsumption(string $type, int $amount, string $unit, string $time, string $target): void
    {
        $this->builder->setConsumption($type, $amount, $unit, $time, $target);
    }

    /**
     * @Given /perform billing for time (\S+) for sales/
     */
    public function performBilling(string $time): void
    {
        $this->builder->performBilling($time);
    }

    /**
     * @Given /bill +for (\S+) is +(\S+) (\S+) per (\d+) (\S+) for target (\S+)$/
     */
    public function bill($type, $sum, $currency, $quantity, $unit, $target)
    {
        $bill = $this->findBill([
            'type' => $type,
            'target' => $target,
            'sum' => "$sum $currency",
            'quantity' => "$quantity $unit",
        ]);
        Assert::assertSame($type, $bill->getType()->getName());
        Assert::assertSame($target, $bill->getTarget()->getType() . ':' . $bill->getTarget()->getName());
        Assert::assertEquals($sum*100, $bill->getSum()->getAmount());
        Assert::assertSame($currency, $bill->getSum()->getCurrency()->getCode());
        Assert::assertEquals($quantity, $bill->getQuantity()->getQuantity());
        Assert::assertSame($unit, $bill->getQuantity()->getUnit()->getName());
    }

    public function findBill(array $params): BillInterface
    {
        $bills = $this->builder->findBills($params);
        $this->bill = reset($bills);
        $this->charges = $this->bill->getCharges();

        return $this->bill;
    }

    /**
     * @Given /bills number is (\d+) for (\S+) for target (\S+)/
     */
    public function billsNumber($number, $type, $target)
    {
        $count = count($this->builder->findBills([
            'type' => $type,
            'target' => $target,
        ]));

        Assert::assertEquals($number, $count);
    }

    /**
     * @Given /charge for (\S+) is +(\S+) (\S+) per (\d+) (\S+) for target (\S+)$/
     */
    public function chargeWithTarget($type, $amount, $currency, $quantity, $unit, $target)
    {
        $charge = $this->getNextCharge();
        Assert::assertSame($type, $charge->getType()->getName());
        Assert::assertSame($target, $charge->getTarget()->getType() . ':' . $charge->getTarget()->getName());
        Assert::assertEquals($amount*100, $charge->getSum()->getAmount());
        Assert::assertSame($currency, $charge->getSum()->getCurrency()->getCode());
        Assert::assertEquals($quantity, $charge->getUsage()->getQuantity());
        Assert::assertSame($unit, $charge->getUsage()->getUnit()->getName());
    }

    /**
     * @Given /charge for (\S+) is +(\S+) (\S+) per (\d+) (\S+)$/
     */
    public function charge($type, $amount, $currency, $quantity, $unit)
    {
        $this->chargeWithTarget($type, $amount, $currency, $quantity, $unit, null);
    }

    public function getNextCharge(): ChargeInterface
    {
        $charge = current($this->charges);
        next($this->charges);

        return $charge;
    }
}
