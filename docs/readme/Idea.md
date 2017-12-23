In general the billing functions like this:

For a given [order] a [calculator] finds [plan]s and then matches
applicable [price]s to [action]s and calculates [charge]s.
Then [charge]s can be aggregated to [bill]s with [aggregator].

Billing operates such ideas:

- [Action] - [customer]'s action of a [type] to a [target]
- [Order]
- [Charge]
- [Bill]
- [Plan]
- [Price]
- [Customer]
- [Sale]
- [Target]
- [Type]
- [Calculator]
- [Aggregator]

[Action]:       /src/action/Action.php
[Bill]:         /src/bill/Bill.php
[Calculator]:   /src/order/Calculator.php
[Charge]:       /src/charge/Charge.php
[Order]:        /src/order/Order.php
[Plan]:         /src/plan/Plan.php
[Price]:        /src/price/AbstractPrice.php
[SinglePrice]:  /src/price/SinglePrice.php
[EnumPrice]:    /src/price/EnumPrice.php
[Sale]:         /src/sale/Sale.php
[Target]:       /src/target/Target.php
[Type]:         /src/target/Type.php
