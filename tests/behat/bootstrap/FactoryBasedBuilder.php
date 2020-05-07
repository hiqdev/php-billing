<?php

namespace hiqdev\php\billing\tests\behat\bootstrap;

use hiqdev\php\billing\price\EnumPrice;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\price\PriceFactory;
use hiqdev\php\billing\order\Order;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\tests\support\tools\SimpleFactory;
use hiqdev\php\billing\tests\support\order\SimpleBilling;
use hiqdev\billing\hiapi\tests\support\order\SimpleCalculator;

class FactoryBasedBuilder implements BuilderInterface
{
    private $reseller;

    private $customer;

    private $time;

    private $plan;

    private $sale;

    private $prices = [];

    private $factory;

    private $calculator;

    private $billing;

    public function __construct()
    {
        $this->factory = new SimpleFactory([
            'price'     => new PriceFactory([
                'certificate,certificate_purchase' => EnumPrice::class,
                'certificate,certificate_renewal' => EnumPrice::class,
            ], SinglePrice::class),
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

    public function buildPlan(string $name, string $type, bool $grouping = false)
    {
        $this->prices = [];
        $this->plan = $this->factory->get('plan', [
            'name' => $name,
            'seller' => $this->reseller,
        ]);
    }

    public function buildPrice(array $data)
    {
        if (!empty($data['price'])) {
            $data['price'] = "$data[price] $data[currency]";
        }
        if (empty($data['prepaid'])) {
            $data['prepaid'] = "0 $data[unit]";
        }
        if (empty($data['target'])) {
            $data['target'] = Target::any();
        }
        $this->prices[] = $this->factory->get('price', $data);
    }

    public function recreatePlan(string $name)
    {
        $plan = $this->factory->get('plan', $name);
        $plan->setPrices($this->prices);
    }

    public function buildSale($id, string $target, string $plan, string $time)
    {
        $this->time = $time;
        $this->sale = $this->factory->get('sale', array_filter([
            'id' => $id,
            'customer' => $this->customer,
            'target' => $target,
            'plan' => $plan,
            'time' => $time,
        ]));

        return $this->sale;
    }

    public function buildPurchase(string $target, string $plan, string $time)
    {
        $this->performAction([
            'sale' => $this->buildSale(null, $target, $plan, $time),
            'type' => 'monthly,cdn_traf95',
            'quantity' => '1 items',
            'target' => $target,
        ]);
    }

    public function buildTarget(string $target)
    {
        return $this->factory->get('target', $target);
    }

    public function performAction(array $data)
    {
        $action = $this->buildAction($data);
        $this->getBilling()->perform(Order::fromActions([$action]));
    }

    public function buildAction(array $data)
    {
        $data['time'] = $data['time'] ?? $this->time;
        $data['customer'] = $data['customer'] ?? $this->customer;
        if (!empty($data['targets'])) {
            $data['target'] = $this->factory->get('targets', $data['targets']);
        }

        return $this->factory->get('action', $data);
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
}
