# PHP Billing Library

[![Latest Stable Version](https://poser.pugx.org/hiqdev/php-billing/v/stable)](https://packagist.org/packages/hiqdev/php-billing)
[![Total Downloads](https://poser.pugx.org/hiqdev/php-billing/downloads)](https://packagist.org/packages/hiqdev/php-billing)
[![codecov](https://codecov.io/gh/hiqdev/php-billing/branch/master/graph/badge.svg)](https://codecov.io/gh/hiqdev/php-billing)
![phpunit-tests](https://github.com/hiqdev/php-billing/actions/workflows/phpunit-tests.yml/badge.svg)
![behat-tests](https://github.com/hiqdev/php-billing/actions/workflows/behat-tests.yml/badge.svg)
![psalm](https://github.com/hiqdev/php-billing/actions/workflows/psalm-analysis.yml/badge.svg)
![phpcs](https://github.com/hiqdev/php-billing/actions/workflows/phpcs.yml/badge.svg)

A pure domain library for billing and invoicing. No framework dependency — drop it into any PHP 8.3+ project.

It models the full billing pipeline: customers subscribe to resources under pricing plans, metered activities are recorded as actions, the calculator matches actions to prices to produce charges, and the aggregator groups charges into bills.

Supports one-time, metered, and recurring charging with multiple pricing strategies (fixed per-unit, percentage-based, tiered/progressive, discrete lookup), a formula DSL for smart discounts and installments, and a reseller hierarchy.

## Installation

```bash
composer require hiqdev/php-billing
```

## How It Works

```
Customer → Plan → Price
                    ↓
Sale → Action → Calculator → Charge → Aggregator → Bill
```

1. A **Customer** subscribes to a **Target** (resource) under a **Plan** via a **Sale**
2. Metered activity is recorded as an **Action** (type, target, quantity, time)
3. **Calculator** matches each Action against Prices in the Plan → produces **Charges**
4. **Aggregator** groups Charges into **Bills** (invoice line items)
5. **Billing** orchestrates steps 3–4 and optionally persists the result

## Core Entities

| Entity | Role |
|--------|------|
| **Action** | A customer's metered activity of a certain Type at a certain Target |
| **Sale** | Subscription binding a Customer to a Target under a Plan |
| **Plan** | A tariff containing a set of Prices |
| **Price** | A billing rule — SinglePrice, EnumPrice, RatePrice, or ProgressivePrice |
| **Charge** | Result of matching an Action to a Price |
| **Bill** | Aggregation of Charges into an invoice line item |
| **Calculator** | Orchestrates the billing pipeline |
| **Aggregator** | Groups Charges into Bills by unique key |
| **Billing** | Top-level entry point: calculate + aggregate + persist |

![Model UML](http://www.plantuml.com/plantuml/proxy?cache=no&src=https://raw.githubusercontent.com/hiqdev/php-billing/master/docs/model.puml)

[Action]:       /src/action/Action.php
[Aggregator]:   /src/tools/Aggregator.php
[Bill]:         /src/bill/Bill.php
[Billing]:      /src/order/Billing.php
[Calculator]:   /src/order/Calculator.php
[Charge]:       /src/charge/Charge.php
[Customer]:     /src/customer/Customer.php
[Order]:        /src/order/Order.php
[Plan]:         /src/plan/Plan.php
[Price]:        /src/price/AbstractPrice.php
[Sale]:         /src/sale/Sale.php
[Target]:       /src/target/Target.php
[Type]:         /src/type/Type.php

## Documentation

- [Domain Model](docs/domain-model.md) — core entities, state objects, immutability rules, calculator pipeline, orchestration
- [Price Types](docs/price-types.md) — SinglePrice, EnumPrice, RatePrice, ProgressivePrice algorithms
- [Formula DSL and Charge Modifiers](docs/formula-and-modifiers.md) — discount, installment, cap, modifier system, ChargeDerivative
- [Product Subsystem](docs/product-subsystem.md) — tariff type registry, behaviors, invoice representations
- [Codebase Overview](docs/overview.md) — directory map, patterns, and testing

## Integration Points

This library is the billing core — a foundation that needs surrounding infrastructure to become a complete billing system. Typical layers built on top:

- **Storage** — implement `PlanRepositoryInterface`, `SaleRepositoryInterface`, `BillRepositoryInterface`, etc. to connect to your database
- **Action producers** — translate business events (purchases, resource measurements) into `Action` objects
- **Scheduler** — periodic jobs that fetch usage metrics, create Actions, run `Billing::perform()`, and update Bills
- **Plan management UI** — lets managers create Plans, configure Prices, and assign Plans to Customers
- **Read API** — query layer for Bills, Charges, Actions with filtering, ordering, and access control

Many of these layers are use-case specific. This package provides the calculation engine; persistence, scheduling, and UI are your responsibility.

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2017-2026, HiQDev (<http://hiqdev.com/>)
