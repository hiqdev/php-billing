# Formula DSL and Charge Modifiers

## Overview

The formula system transforms human-readable DSL strings into `ChargeModifier` objects that modify billing charges. Prices can have formulas attached to apply discounts, installments, caps, and other modifications.

Pipeline: `formula string → FormulaEngine → Hoa\Ruler AST → ChargeModifier`

## FormulaEngine

`FormulaEngine` (`src/formula/FormulaEngine.php`) parses and evaluates formula strings:

1. **Normalize** — trims whitespace, joins multi-line formulas with ` AND `
2. **Interpret** — parses string into Hoa\Ruler AST Model (cached via PSR SimpleCache)
3. **Assert** — evaluates AST against context variables, producing a `ChargeModifier`

```php
$engine = new FormulaEngine($cache);
$modifier = $engine->build("discount.fixed('25 USD').reason('bulk')");
$engine->validate($formula); // returns null if valid, error message otherwise
```

**Context variables** available in formula strings: `discount`, `installment`, `increase`, `cap`, `once`.

## Attaching Formulas to Prices

`AbstractPrice` implements `ChargeModifier` via `SettableChargeModifierTrait`:

```php
$price->setModifier($modifier);
// During calculation, Calculator calls $price->modifyCharge($charge, $action)
// which delegates to the attached modifier
```

When no modifier is set, `modifyCharge()` returns `[$charge]` unchanged.

## ChargeModifier Interface

```php
interface ChargeModifier {
    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array;
    public function isSuitable(?ChargeInterface $charge, ActionInterface $action): bool;
}
```

Key invariant: `modifyCharge()` returns an array of charges — typically the original charge plus modifier charges (e.g., a discount charge with negative sum).

## Modifier Classes

Base class `Modifier` provides addon management and time-bound checking (`since`, `till`, `lasts`).

### FixedDiscount

Fixed absolute or percentage discount.

```
discount.fixed('25 USD').reason('TEST')
discount.since('08.2018').fixed('20%')
discount.since('08.2018').till('09.2018').fixed('20%')
discount.fixed('20%').since('08.2018').lasts('2 months')
```

Returns `[$originalCharge, $discountCharge]` when applicable. Discount charge has negative sum.

### GrowingDiscount

Discount that increases over time by a step amount per period.

```
discount.since('08.2018').grows('1%').every('month').min('10 USD')
discount.since('08.2000').grows('30%').every('year').max('100%')
discount.since('08.2018').grows('20 USD').every('2 months').min('15 USD').max('80 USD')
discount.since('08.2018').till('12.2018').grows('10pp').every('month')
```

Supports absolute (`USD`), relative (`%`), and percentage point (`pp`) steps. `min`/`max` cap the accumulated discount.

### Increase

Like GrowingDiscount but with inverted sign (price goes up instead of down).

```
increase.since('08.2018').till('12.2018').grows('30%').every('month')
```

### Installment

Spreads a charge over a fixed term as monthly payments.

```
installment.since('08.2018').lasts('3 months').reason('TEST')
```

Returns a single charge with type `leasing` (pre-2024) or `installment` (2024+). The `till()` method is forbidden — use `lasts()` instead. Records domain events `InstallmentWasStarted` / `InstallmentWasFinished`.

### Cap / MonthlyCap

Limits maximum billable usage per month.

```
cap.monthly('28 days')
cap.monthly('28 days').since('11.2020')
cap.monthly('28 days').since('11.2020').forNonProportionalizedQuantity()
```

Splits charges at the cap boundary: usage within cap is charged normally, usage above cap produces a zero charge.

### Once

Bills only once per interval (e.g., yearly).

```
once.per('1 year').since('01.2020')
```

Returns the original charge if the current month matches the interval, otherwise returns a zero charge.

## Combining Modifiers

Multiple modifiers are combined using `AND` (multi-line formulas):

```
discount.since('08.2018').fixed('30%').reason('ONE')
discount.since('10.2018').fixed('10 USD').reason('TWO')
discount.since('12.2018').fixed('50%').reason('THREE')
```

Lines are joined with AND and parsed into a `FullCombination` tree.

### FullCombination

Applies both modifiers sequentially — left modifier first, then right modifier on the combined result. Both produce charges that are merged.

### LastCombination

First-match-wins: applies right modifier if suitable, otherwise falls back to left.

## Addon System

Modifiers use a composable addon system for configuration:

| Addon | Purpose |
|-------|---------|
| `Since` / `Till` | Time bounds for modifier applicability |
| `MonthPeriod` / `YearPeriod` / `DayPeriod` | Time periods for `every()` and `lasts()` |
| `Discount` | Discount value (absolute, relative %, or percentage point) |
| `Step` | Growth step for GrowingDiscount |
| `Reason` | Human-readable comment attached to modifier charges |
| `Minimum` / `Maximum` | Bounds for accumulated discount values |

Fluent API via traits: `WithSince`, `WithTill`, `WithReason`, `WithTerm`, `WithChargeType`.
