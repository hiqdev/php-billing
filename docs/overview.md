# Codebase Overview

## Directory Map

```
src/
├── action/        Action entity — metered activity that gets charged
├── bill/          Bill entity — aggregated invoice line item
├── charge/        Charge entity, ChargeModifier interface, modifier classes and addons
├── customer/      Customer entity — billable party with seller hierarchy
├── event/         Domain events (e.g., InstallmentWasStarted)
├── Exception/     Shared exceptions (CannotReassignException, etc.)
├── formula/       FormulaEngine — parses DSL strings into ChargeModifier objects
├── helpers/       Utility classes
├── Money/         Money value object, MultipliedMoney for sub-cent prices
├── order/         Order (action collection), Calculator (main billing pipeline)
├── plan/          Plan entity — tariff containing a collection of Prices
├── price/         Price implementations (SinglePrice, EnumPrice, RatePrice, ProgressivePrice)
├── product/       Product-related interfaces
├── sale/          Sale entity — subscription binding Customer → Target → Plan
├── statement/     Statement-related classes
├── target/        Target entity — object being billed
├── tools/         Aggregator (Charge → Bill grouping), Generalizer (Charge → Bill mapping)
├── type/          Type entity — classification of actions and charges
└── usage/         Usage tracking classes
```

## Key Patterns

- **Interface + Implementation**: core entities define interfaces (e.g., `PriceInterface`, `BillInterface`)
- **Value Objects**: Money, Quantity, Target, Type are immutable value objects
- **Trait Composition**: `HasMoney`, `HasQuantity`, `SettableChargeModifierTrait`, fluent `With*` traits for modifier addons
- **Factory**: `ModifierFactory` for formula context, static `::fromAction()` / `::fromActions()` on Order
- **Strategy**: Price types implement different calculation strategies via `calculateSum()` / `calculateUsage()`
- **Immutability Guards**: `CannotReassignException` prevents mutation of billing-critical fields

## Testing

- **Behat features** in `tests/behat/` — living documentation for the formula DSL (FixedDiscount, GrowingDiscount, Installment, MonthlyCap, Combination)
- **PHPUnit tests** in `tests/unit/` — unit tests for prices, formulas, modifiers, and entities

## Further Reading

- For formula DSL and charge modifiers, see [docs/formula-and-modifiers.md](formula-and-modifiers.md)
- For core entities and execution flow, see [docs/domain-model.md](domain-model.md)
- For price type algorithms, see [docs/price-types.md](price-types.md)
