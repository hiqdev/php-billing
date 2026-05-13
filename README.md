# PHP Billing Library

[![Latest Stable Version](https://poser.pugx.org/hiqdev/php-billing/v/stable)](https://packagist.org/packages/hiqdev/php-billing)
[![Total Downloads](https://poser.pugx.org/hiqdev/php-billing/downloads)](https://packagist.org/packages/hiqdev/php-billing)
![phpunit-tests](https://github.com/hiqdev/php-billing/actions/workflows/phpunit-tests.yml/badge.svg)
![behat-tests](https://github.com/hiqdev/php-billing/actions/workflows/behat-tests.yml/badge.svg)

A pure domain library for billing and invoicing. It models the full billing pipeline:
[Customer]s subscribe to [Target]s under [Plan]s (via [Sale]s), metered activities
are recorded as [Action]s, the [Calculator] matches Actions to [Price]s within Plans
to produce [Charge]s, and the [Aggregator] groups Charges into [Bill]s.

The library supports one-time, metered, and recurring charging with multiple pricing
strategies (fixed per-unit, percentage-based, tiered/progressive, discrete lookup),
a formula DSL for smart discounts and installments, and a reseller hierarchy.

No framework dependency — this is a standalone domain model.

## Core Entities

- **[Action]** — a [Customer]'s metered activity of a certain [Type] at a certain [Target]
- **[Order]** — a collection of Actions to be billed together
- **[Sale]** — a subscription binding a [Customer] to a [Target] under a [Plan]
- **[Plan]** — a tariff containing a set of [Price]s
- **[Price]** — a billing rule that calculates charges (SinglePrice, EnumPrice, RatePrice, ProgressivePrice)
- **[Charge]** — the result of matching an Action to a Price
- **[Bill]** — aggregation of Charges into an invoice line item
- **[Calculator]** — orchestrates the billing pipeline
- **[Aggregator]** — groups Charges into Bills

![Model UML](http://www.plantuml.com/plantuml/proxy?cache=no&src=https://raw.githubusercontent.com/hiqdev/php-billing/master/docs/model.puml)

[Action]:       /src/action/Action.php
[Aggregator]:   /src/tools/Aggregator.php
[Bill]:         /src/bill/Bill.php
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

- [Domain Model](docs/domain-model.md) — core entities, matching rules, immutability, calculator pipeline
- [Price Types](docs/price-types.md) — SinglePrice, EnumPrice, RatePrice, ProgressivePrice algorithms
- [Formula DSL and Charge Modifiers](docs/formula-and-modifiers.md) — discount, installment, cap, and modifier system
- [Codebase Overview](docs/overview.md) — directory map, patterns, and testing

## Disclaimer

This billing is designed to be flexible and abstract, so supports different use cases.
We use this package in production, wrapping it additional layers, such as:

1. Plan and Price storage and management UI for managers, so they can create plans,
   fill them with prices and assign to Customers.
2. Actions and Orders producer. This layer takes end-user actions
   (such as purchasing something) and produces the right Actions inside the Order
3. Persistence layer. This layer implements various RepositoryInterfaces,
   defined in this package (such as PlanRepositoryInterface,
   providing data saving and retrieving logic for the required entities.
4. Periodic operations (CRON tasks). This includes meters fetching
   (such as accumulated resources consumption), transforming them to actions
   with the right quantity, running billing on them, updating Bills and their Charges.
5. Business metrics monitoring, analysis and alerting.
   This layer provides regular checks over data, produces by the billing in order to ensure system health.
6. Read API. This API accepts requests, fetches data from the DBMS
   and implements search with filtering, ordering, access control
   and more for Orders, Actions, Bills, Prices and so on.

So, as you can see, this package is a concrete foundation of big billing system,
but it requires a lot of bricks on top of it to become a fully operable billing.
Unfortunately, we do not have all those bricks open-sourced and documented
because many of them implement customer-specific logic that cannot be disclosed.

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2017-2026, HiQDev (<http://hiqdev.com/>)
