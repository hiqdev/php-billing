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

use Behat\Gherkin\Node\TableNode;
use BehatExpectException\ExpectException;
use DateTimeImmutable;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use PHPUnit\Framework\Assert;

class BillingContext extends BaseContext
{
    use ExpectException {
        mayFail as protected;
        shouldFail as protected;
        assertCaughtExceptionMatches as protected;
    }

    protected $saleTime;

    protected $bill;

    protected $charges = [];

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
     * @Given /^(\S+ )?(\S+) tariff plan (\S+)/
     */
    public function plan($prefix, $type, $plan)
    {
        $prefix = strtr($prefix, ' ', '_');
        $grouping = $prefix === 'grouping_';
        $type = $grouping ? $type : $prefix.$type;
        $this->builder->buildPlan($plan, $type, $grouping);
    }

    protected function fullPrice(array $data)
    {
        if (!empty($data['price'])) {
            $data['rate'] = $data['price'];
        }
        $this->builder->buildPrice($data);
    }

    /**
     * @Given /price for (\S+) is +(\S+) (\S+) per (\S+) for target (.+)$/
     */
    public function priceWithTarget($type, $price, $currency, $unit, $target)
    {
        return $this->fullPrice(compact('type', 'price', 'currency', 'unit', 'target'));
    }

    /**
     * @Given /price for (\S+) is +(\S+) (\S+) per (\S+) prepaid (\S+)$/
     */
    public function priceWithPrepaid($type, $price, $currency, $unit, $prepaid)
    {
        return $this->fullPrice(compact('type', 'price', 'currency', 'unit', 'prepaid'));
    }

    /**
     * @Given /price for (\S+) is +(\S+) (\S+) per (\S+) prepaid (\S+) for target (\S+)$/
     */
    public function priceWithPrepaidAndTarget($type, $price, $currency, $unit, $prepaid, $target)
    {
        return $this->fullPrice(compact('type', 'price', 'currency', 'unit', 'prepaid', 'target'));
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
     * @Given /sale target (\S+) by plan (\S+) at (\S+)/
     */
    public function sale($target, $plan, $time): void
    {
        $this->saleTime = $this->prepareTime($time);
        $this->builder->buildSale($target, $plan, $this->saleTime);
    }
    /**
     * @When /^sale close is requested for target "([^"]*)" at "([^"]*)", assuming current time is "([^"]*)"$/
     */
    public function saleClose(string $target, string $time, ?string $wallTime)
    {
        throw new PendingException();
    }

    /**
     * @Then /^target "([^"]*)" has exactly (\d+) sale for customer$/
     */
    public function targetHasExactlyNSaleForCustomer(string $target, string $count)
    {
        // TODO: implement
        // $sales = $this->builder->findSales(['target-name' => $target]);

        Assert::assertCount($count, $sales);
    }

    /**
     * @Given /purchase target (\S+) by plan (\S+) at (\S+)$/
     */
    public function purchaseTarget(string $target, string $plan, string $time): void
    {
        $time = $this->prepareTime($time);
        $this->builder->buildPurchase($target, $plan, $time);
    }

    /**
     * @Given /^purchase target "([^"]*)" by plan "([^"]*)" at "([^"]*)" with the following initial uses:$/
     */
    public function purchaseTargetWithInitialUses(string $target, string $plan, string $time, TableNode $usesTable): void
    {
        $time = $this->prepareTime($time);
        $uses = array_map(static function (array $row) {
            return [
                'type' => $row['type'],
                'unit' => $row['unit'],
                'amount' => $row['amount'],
            ];
        }, $usesTable->getColumnsHash());

        $this->mayFail(
            fn() => $this->builder->buildPurchase($target, $plan, $time, $uses)
        );
    }

    /**
     * @Given /resource consumption for (\S+) is +(\S+) (\S+) for target (\S+) at (.+)$/
     */
    public function setConsumption(string $type, int $amount, string $unit, string $target, string $time): void
    {
        $time = $this->prepareTime($time);
        $this->builder->setConsumption($type, $amount, $unit, $target, $time);
    }

    /**
     * @Given /recalculate autotariff for target (\S+)( +at (\S+))?$/
     */
    public function recalculateAutoTariff(string $target, string $time = null): void
    {
        $this->builder->clientSetAutoTariff($target, $time);
    }

    /**
     * @Given /perform billing at (\S+)/
     */
    public function performBilling(string $time): void
    {
        $this->builder->performBilling($this->prepareTime($time));
    }

    /**
     * @Given /action for (\S+) is +(\S+) (\S+) +for target (.+?)( +at (\S+))?$/
     */
    public function setAction(string $type, int $amount, string $unit, string $target, string $at = null, string $time = null): void
    {
        $time = $this->prepareTime($time);
        $this->builder->setAction($type, $amount, $unit, $target, $time);
    }

    /**
     * @Given /perform calculation( at (\S+))?/
     */
    public function performCalculation(string $at = null, string $time = null): array
    {
        $this->charges = $this->builder->performCalculation($this->prepareTime($time));
        return $this->charges;
    }

    /**
     * @Given /bill +for (\S+) is +(\S+) (\S+) per (\S+) (\S+) for target (.+?)( +at (.+))?$/
     */
    public function billWithTime($type, $sum, $currency, $quantity, $unit, $target, $at = null, $time = null)
    {
        $this->builder->flushEntitiesCacheByType('bill');

        $quantity = $this->prepareQuantity($quantity);
        $sum = $this->prepareSum($sum, $quantity);
        $time = $this->prepareTime($time);
        $bill = $this->findBill([
            'type' => $type,
            'target' => $target,
            'sum' => "$sum $currency",
            'quantity' => "$quantity $unit",
            'time' => $time,
        ]);
        Assert::assertSame($type, $bill->getType()->getName(), "Bill type mismatch: expected $type, got {$bill->getType()->getName()}");
        Assert::assertSame($target, $bill->getTarget()->getFullName(), "Bill target mismatch: expected $target, got {$bill->getTarget()->getFullName()}");
        Assert::assertEquals(bcmul($sum, 100), $bill->getSum()->getAmount(), "Bill sum mismatch: expected $sum, got {$bill->getSum()->getAmount()}");
        Assert::assertSame($currency, $bill->getSum()->getCurrency()->getCode(), "Bill currency mismatch: expected $currency, got {$bill->getSum()->getCurrency()->getCode()}");
        Assert::assertEquals((float)$quantity, (float)$bill->getQuantity()->getQuantity(), "Bill quantity mismatch: expected $quantity, got {$bill->getQuantity()->getQuantity()}");
        Assert::assertEquals(strtolower($unit), strtolower($bill->getQuantity()->getUnit()->getName()), "Bill unit mismatch: expected $unit, got {$bill->getQuantity()->getUnit()->getName()}");
        if ($time) {
            Assert::assertEquals(new DateTimeImmutable($time), $bill->getTime(), "Bill time mismatch: expected $time, got {$bill->getTime()->format(DATE_ATOM)}");
        }
    }

    public function findBill(array $params): BillInterface
    {
        $bills = $this->builder->findBills($params);
        $this->bill = reset($bills);
        $this->charges = $this->bill->getCharges();

        return $this->bill;
    }

    /**
     * @Given /bills number is (\d+) for (\S+) for target (.+?)( +at (\S+))?$/
     */
    public function billsNumberWithTime($number, $type, $target, $at = null, $time = null)
    {
        $count = count($this->builder->findBills(array_filter([
            'type' => $type,
            'target' => $target,
            'time' => $this->prepareTime($time),
        ])));

        Assert::assertEquals($number, $count);
    }

    /**
     * @Given /charges number is (\d+)/
     */
    public function chargesNumber($number)
    {
        Assert::assertEquals($number, count($this->charges));
    }

    /**
     * @Given /charge for (\S+) is +(\S+) (\S+) per (\S+) (\S+)$/
     */
    public function charge($type, $amount, $currency, $quantity, $unit)
    {
        $this->chargeWithTarget($type, $amount, $currency, $quantity, $unit, null);
    }

    /**
     * @Given /charge for (\S+) is +(\S+) (\S+) per +(\S+) (\S+) +for target (.+?)( +at (\S+))?$/
     */
    public function chargeWithTarget($type, $amount, $currency, $quantity, $unit, $target, $at = null, $time = null)
    {
        $quantity = $this->prepareQuantity($quantity);
        $amount = $this->prepareSum($amount, $quantity);
        $charge = $this->findCharge($type, $target);
        Assert::assertNotNull($charge);
        Assert::assertSame($type, $charge->getType()->getName());
        Assert::assertSame($target, $charge->getTarget()->getFullName());
        Assert::assertEquals(bcmul($amount, 100), (int)$charge->getSum()->getAmount());
        Assert::assertSame($currency, $charge->getSum()->getCurrency()->getCode());
        Assert::assertEquals((float)$quantity, (float)$charge->getUsage()->getQuantity());
        Assert::assertEquals(strtolower($unit), strtolower($charge->getUsage()->getUnit()->getName()));
    }

    public function findCharge($type, $target): ?ChargeInterface
    {
        foreach ($this->charges as $charge) {
            if ($charge->getType()->getName() !== $type) {
                continue;
            }
            if ($charge->getTarget()->getFullName() !== $target) {
                continue;
            }

            return $charge;
        }

        return null;
    }

    public function getNextCharge(): ChargeInterface
    {
        $charge = current($this->charges);
        next($this->charges);

        return $charge;
    }

    /**
     * @return string|false|null
     */
    protected function prepareTime(string $time = null)
    {
        if ($time === null) {
            return null;
        }

        if ($time === 'midnight second day of this month') {
            return date('Y-m-02');
        }
        if (strncmp($time, 'pY', 1) === 0) {
            return date(substr($time, 1), strtotime('-1 year'));
        }
        if (str_contains($time, 'nm')) {
            $format = str_replace('nm', 'm', $time);
            return date($format, strtotime('next month'));
        }
        if (str_contains($time, 'pm')) {
            $time = str_replace('pm', 'm', $time);
            $time = date($time, strtotime('-1 month'));
        }
        if (strncmp($time, 'Y', 1) === 0) {
            return date($time);
        }

        return $time;
    }

    private function prepareQuantity($quantity)
    {
        if ($quantity[0] === 's') {
            return $this->getSaleQuantity();
        }

        return $quantity;
    }

    private function prepareSum($sum, $quantity)
    {
        if ($sum[0] === 's') {
            $sum = round(substr($sum, 1) * $quantity*100)/100;
        }

        return $sum;
    }

    public function getSaleQuantity()
    {
        return $this->days2quantity(new DateTimeImmutable($this->saleTime));
    }

    private function days2quantity(DateTimeImmutable $from)
    {
        $till = new DateTimeImmutable('first day of next month midnight');
        $diff = $from->diff($till);
        if ($diff->m) {
            return 1;
        }

        return $diff->d/date('t');
    }

    /**
     * @When /^tariff plan change is requested for target "([^"]*)" to plan "([^"]*)" at "([^"]*)"$/
     */
    public function tariffPlanChangeIsRequestedForTarget(string $target, string $planName, string $date)
    {
        $this->mayFail(fn () => $this->builder->targetChangePlan($target, $planName, $this->prepareTime($date)));
    }

    /**
     * @When /^tariff plan change is requested for target "([^"]*)" to plan "([^"]*)" at "([^"]*)", assuming current time is "([^"]*)"$/
     */
    public function tariffPlanChangeIsRequestedForTargetAtSpecificTime(string $target, string $planName, string $date, ?string $wallTime = null)
    {
        $this->mayFail(fn () => $this->builder->targetChangePlan($target, $planName, $this->prepareTime($date), $this->prepareTime($wallTime)));
    }

    /**
     * @Then /^target "([^"]*)" is sold to customer by plan "([^"]*)" since "([^"]*)"(?: till "([^"]*)")?$/
     */
    public function targetIsSoldToCustomerByPlanSinceTill(string $target, string $planName, string $saleDate, ?string $saleCloseDate = null)
    {
        $sales = $this->builder->findHistoricalSales([
            'target' => $target,
        ]);

        $saleDateTime = new DateTimeImmutable('@' . strtotime($this->prepareTime($saleDate)));
        $saleCloseDateTime = $saleCloseDate ? new DateTimeImmutable('@' . strtotime($this->prepareTime($saleCloseDate))) : null;

        foreach ($sales as $sale) {
            /** @noinspection PhpBooleanCanBeSimplifiedInspection */
            $saleExists = true
                && str_contains($sale->getPlan()->getName(), $planName)
                && $sale->getTime()->format(DATE_ATOM) === $saleDateTime->format(DATE_ATOM)
                && (
                    ($saleCloseDate === null && $sale->getCloseTime() === null)
                    ||
                    ($saleCloseDate !== null && $sale->getCloseTime()->format(DATE_ATOM) === $saleCloseDateTime->format(DATE_ATOM))
                );

            if ($saleExists) {
                return;
            }
        }

        Assert::fail('Requested sale does not exist');
    }

    /**
     * @Then /^target "([^"]*)" has exactly (\d+) sales for customer$/
     */
    public function targetHasExactlySalesForCustomer(string $target, int $count)
    {
        $sales = $this->builder->findHistoricalSales([
            'target' => $target,
        ]);

        Assert::assertCount($count, $sales);
    }

    /**
     * @Then /^caught error is "([^"]*)"$/
     */
    public function caughtErrorIs(string $errorMessage): void
    {
        $this->assertCaughtExceptionMatches(\Throwable::class, $errorMessage);
    }

    /**
     * @Given /^target "([^"]*)"$/
     */
    public function target(string $target)
    {
        $this->builder->buildTarget($target);
    }

    /**
     * @Then /^flush entities cache$/
     */
    public function flushEntitiesCache()
    {
        $this->builder->flushEntitiesCache();
    }

    /**
     * @Given /^target "([^"]*)" has the following uses:$/
     */
    public function targetHasTheFollowingUses(string $target, TableNode $usesTable)
    {
        foreach ($usesTable->getColumnsHash() as $row) {
            $uses = $this->builder->findUsage($row['time'], $target, $row['type']);
            Assert::assertCount(1, $uses);

            $use = reset($uses);
            Assert::assertSame(
                $row['unit'], $use['unit'],
                sprintf('Exptected unit to be %s, got %s instead', $row['unit'], $use['unit'])
            );
            Assert::assertEquals(
                $row['amount'], $use['total'],
                sprintf('Exptected total to be %s, got %s instead', $row['amount'], $use['total'])
            );
        }
    }
}
