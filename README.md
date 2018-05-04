# PHP Billing

**PHP Billing Library**

[![Latest Stable Version](https://poser.pugx.org/hiqdev/php-billing/v/stable)](https://packagist.org/packages/hiqdev/php-billing)
[![Total Downloads](https://poser.pugx.org/hiqdev/php-billing/downloads)](https://packagist.org/packages/hiqdev/php-billing)
[![Build Status](https://img.shields.io/travis/hiqdev/php-billing.svg)](https://travis-ci.org/hiqdev/php-billing)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/hiqdev/php-billing.svg)](https://scrutinizer-ci.com/g/hiqdev/php-billing/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/hiqdev/php-billing.svg)](https://scrutinizer-ci.com/g/hiqdev/php-billing/)
[![Dependency Status](https://www.versioneye.com/php/hiqdev:php-billing/dev-master/badge.svg)](https://www.versioneye.com/php/hiqdev:php-billing/dev-master)

Billing library providing:

- customers with subscriptions
- orders with actions
- tariff plans with prices
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

- [Action] - [customer]'s action of a [type] to a [target]
- [Order] - collection of [action]s
- [Charge]
- [Bill]
- [Plan]
- [Price]
- [Customer]
- [Sale] - binds [customer] to [target] and [plan]
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

## License

This project is released under the terms of the BSD-3-Clause [license](LICENSE).
Read more [here](http://choosealicense.com/licenses/bsd-3-clause).

Copyright © 2017-2018, HiQDev (http://hiqdev.com/)
