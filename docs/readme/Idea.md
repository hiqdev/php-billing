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

![model](https://raw.githubusercontent.com/hiqdev/php-billing/master/docs/model.png)

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
