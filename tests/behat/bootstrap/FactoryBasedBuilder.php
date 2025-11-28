<?php
declare(strict_types=1);
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\behat\bootstrap;

use hiqdev\billing\hiapi\plan\PlanFactory;
use hiqdev\billing\hiapi\tests\support\order\SimpleCalculator;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\price\EnumPrice;
use hiqdev\php\billing\price\PriceFactory;
use hiqdev\php\billing\price\RatePrice;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\tests\support\order\SimpleBilling;
use hiqdev\php\billing\tests\support\tools\SimpleFactory;
use RuntimeException;

class FactoryBasedBuilder implements BuilderInterface
{
    private $reseller;

    private $customer;

    private $time;

    private $plan;

    private SaleInterface $sale;

    private $prices = [];

    /** @var ActionInterface[] */
    private array $actions = [];

    private $factory;

    private $calculator;

    private $billing;

    public function __construct()
    {
        $this->factory = new SimpleFactory([
            'price'     => new PriceFactory([
                'certificate,certificate_purchase' => EnumPrice::class,
                'certificate,certificate_renewal' => EnumPrice::class,
                'referral,referral' => RatePrice::class,
            ], SinglePrice::class),
            'plan' => new PlanFactory(),
        ]);
    }

    private function getBilling()
    {
        if ($this->billing === null) {
            $this->billing = new SimpleBilling($this->getCalculator());
        }

        return $this->billing;
    }

    private function getCalculator()
    {
        if ($this->calculator === null) {
            $this->calculator = new SimpleCalculator(null, $this->sale, $this->plan);
        }

        return $this->calculator;
    }

    public function buildReseller(string $login)
    {
        $this->reseller = $login;
        $this->factory->get('customer', $login);
    }

    public function buildCustomer(string $login)
    {
        $this->customer = $login;
        $this->factory->get('customer', [
            'login' => $login,
            'seller' => $this->reseller,
        ]);
    }

    public function buildPlan(string $name, string $type, bool $is_grouping = false)
    {
        $this->prices = [];
        $this->plan = $this->factory->get('plan', [
            'id' => $name,
            'name' => $name,
            'seller' => $this->reseller,
            'is_grouping' => $is_grouping,
        ]);
    }

    public function buildPrice(array $data)
    {
        if (!empty($data['price'])) {
            $data['price'] = "$data[price] $data[currency]";
        }
        $data['prepaid'] = ($data['prepaid'] ?? 0) . " $data[unit]";
        if (empty($data['target'])) {
            $data['target'] = Target::any();
        }
        if (empty($data['plan'])) {
            $data['plan'] = $this->plan;
        }
        $this->prices[] = $this->factory->get('price', $data);
    }

    public function recreatePlan(string $name)
    {
        $plan = $this->factory->get('plan', $name);
        $plan->setPrices($this->prices);
    }

    public function buildSale(string $target, $planName, string $time = null, ?string $closeTime = null)
    {
        return $this->createSale($target, $planName, $time, $closeTime);
    }

    public function createSale(string $target, $planName, string $time = null, ?string $closeTime = null)
    {
        $this->time = $time;
        $this->sale = $this->factory->getSale(array_filter([
            'customer' => $this->customer,
            'target' => $target,
            'plan' => $planName,
            'time' => $time,
            'closeTime' => $closeTime,
        ]));

        return $this->sale;
    }

    public function setConsumption($type, $amount, $unit, $target, $time)
    {
        $this->actions[] = $this->buildAction([
            'type' => $type,
            'quantity' => "$amount $unit",
            'target' => $target,
            'time' => $time,
        ]);
    }

    public function setAction(string $type, int $amount, string $unit, string $target, string $time): void
    {
        $this->actions[] = $this->buildAction([
            'type' => $type,
            'quantity' => "$amount $unit",
            'target' => $target,
            'time' => $time,
            'sale' => $this->buildSale($target, $this->plan, $time),
        ]);
    }

    public function buildPurchase(string $target, string $plan, string $time, ?array $uses = [])
    {
        $this->performAction([
            'sale' => $this->buildSale($target, $plan, $time),
            'type' => 'monthly,cdn_traf95_max',
            'quantity' => '1 items',
            'target' => $target,
            'initial_uses' => $uses,
        ]);
    }

    public function buildTarget(string $target)
    {
        return $this->factory->get('target', $target);
    }

    public function performCalculation(string $time = null): array
    {
        return $this->getBilling()->calculateCharges($this->actions);
    }

    public function performBilling(string $time): void
    {
        $this->getBilling()->perform($this->actions);
        #$bills = $this->getBilling()->getBillRepository()->findAll(new Specification);
        #var_dump(__FILE__ . ':' . __LINE__ . ' ' . __METHOD__, $bills);die;
        #$b1 = reset($bills);
        #var_dump(__FILE__ . ':' . __LINE__ . ' ' . __METHOD__, $b1->getCharges());die;
    }

    public function performAction(array $data)
    {
        $action = $this->buildAction($data);
        $this->getBilling()->perform($action);
    }

    public function buildAction(array $data): ActionInterface
    {
        $data['time'] = $data['time'] ?? $this->time;
        $data['customer'] = $data['customer'] ?? $this->customer;
        if (!empty($data['targets'])) {
            $data['target'] = $this->factory->getTargets($data['targets']);
        }

        return $this->factory->getAction($data);
    }

    public function findBills(array $data): array
    {
        $data['sum'] = $data['sum'] ?? '0 USD';
        $data['quantity'] = $data['quantity'] ?? '0 items';
        $bill = $this->buildBill($data);
        $repo = $this->getBilling()->getBillRepository();

        return $repo->findByUniqueness([$bill]);
    }

    public function buildBill(array $data)
    {
        $data['time'] = $data['time'] ?? $this->time;
        $data['customer'] = $data['customer'] ?? $this->customer;
        if (!empty($data['targets'])) {
            $data['target'] = $this->factory->get('targets', $data['targets']);
        }

        return $this->factory->get('bill', $data);
    }

    public function findCharges(array $data): array
    {
        $data['sum'] = $data['sum'] ?? '0 USD';
        $data['quantity'] = $data['quantity'] ?? '0 items';
        $bill = $this->buildCharge($data);
        $repo = $this->getBilling()->getChargeRepository();

        return $repo->findByUniqueness($bill);
    }

    public function buildCharge(array $data)
    {
        $data['time'] = $data['time'] ?? $this->time;
        $data['customer'] = $data['customer'] ?? $this->customer;
        if (!empty($data['targets'])) {
            $data['target'] = $this->factory->get('targets', $data['targets']);
        }

        return $this->factory->get('bill', $data);
    }

    public function targetChangePlan(string $target, string $planName, string $date, string $wallTime = null)
    {
        throw new RuntimeException('Not implemented yet');
    }

    public function findSales(array $params)
    {
        $keys = $this->factory->getEntityUniqueKeys('sale');

        return $this->factory->find('sale', $keys);
    }

    public function findHistoricalSales(array $params)
    {
        $keys = $this->factory->getEntityUniqueKeys('sale');

        return $this->factory->find('sale', $keys);
    }

    public function flushEntitiesCache(): void
    {
        $this->factory->clearEntitiesCache();
    }

    public function flushEntitiesCacheByType(string $type): void
    {
        $this->factory->clearEntitiesCacheByType($type);
    }

    public function findUsage(string $time, string $targetName, string $typeName): array
    {
        throw new RuntimeException('Not implemented yet');
    }
}
