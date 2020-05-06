<?php

namespace hiqdev\php\billing\tests\behat\bootstrap;

interface BuilderInterface
{
    public function buildReseller(string $login);

    public function buildCustomer(string $login);

    public function buildPlan(string $name, string $type, bool $grouping = false);

    public function buildPrice(array $data);

    public function recreatePlan(string $name);

    public function buildSale(string $id, string $target, string $plan, string $time);

    public function buildPurchase(string $target, string $plan, string $time);

    public function findBills(array $data): array;
}
