# PHP Billing

**PHP Billing Library**

[![Latest Stable Version](https://poser.pugx.org/hiqdev/php-billing/v/stable)](https://packagist.org/packages/hiqdev/php-billing)
[![Total Downloads](https://poser.pugx.org/hiqdev/php-billing/downloads)](https://packagist.org/packages/hiqdev/php-billing)
[![Build Status](https://img.shields.io/travis/hiqdev/php-billing.svg)](https://travis-ci.org/hiqdev/php-billing)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/hiqdev/php-billing.svg)](https://scrutinizer-ci.com/g/hiqdev/php-billing/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/hiqdev/php-billing.svg)](https://scrutinizer-ci.com/g/hiqdev/php-billing/)

Billing library providing:

- customers with subscriptions
- orders with actions
- tariff plans with prices
- smart discounts with formulas
- bills with charges
- calculator and aggregator

- one-time, metered and recurring charging

Please see [additional doccumentation in russian](docs/ru.md).

## Installation

The preferred way to install this library is through [composer](http://getcomposer.org/download/).

Either run

```sh
php composer.phar require "hiqdev/php-billing"
```

or add

```json
"hiqdev/php-billing": "*"
```

to the require section of your composer.json.

## Idea

In general the billing functions like this:

For a given [order] a [calculator] finds [plan]s and then matches
applicable [price]s to [action]s and calculates [charge]s.
Then [charge]s can be aggregated to [bill]s with [aggregator].

Billing operates such ideas:

- [Action] - [customer]'s metered activity of a certain [type] to a certain [target]
- [Order] - collection of [action]s
- [Bill]
- [Charge]
- [Plan]
- [Price]
- [Customer]
- [Sale] - a subscription, binding [customer] to a [target] and a [plan]
- [Target] - object being charged in billing
- [Type]
- [Calculator]
- [Aggregator]

[Action]:       /src/action/Action.php
[Aggregator]:   /src/charge/Aggregator.php
[Bill]:         /src/bill/Bill.php
[Calculator]:   /src/order/Calculator.php
[Charge]:       /src/charge/Charge.php
[Customer]:     /src/customer/Customer.php
[Order]:        /src/order/Order.php
[Plan]:         /src/plan/Plan.php
[Price]:        /src/price/AbstractPrice.php
[SinglePrice]:  /src/price/SinglePrice.php
[EnumPrice]:    /src/price/EnumPrice.php
[Sale]:         /src/sale/Sale.php
[Target]:       /src/target/Target.php
[Type]:         /src/target/Type.php

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

Copyright © 2017-2019, HiQDev (http://hiqdev.com/)
