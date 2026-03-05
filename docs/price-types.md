# Price Types

## Overview

Four price type implementations, each with different calculation strategies.

## SinglePrice

Fixed per-unit rate: `price × quantity = sum`.

Use for: monthly fees, per-item charges, fixed-rate resources.

## EnumPrice

Discrete lookup table: maps specific quantity values to specific sums.

Use for: tiered pricing with exact breakpoints, discrete service levels.
Throws exception if quantity doesn't match any entry.

## RatePrice

Percentage-based calculation.

Use for: referral commissions, rebates, percentage discounts.

## ProgressivePrice

Tiered pricing with thresholds and prepaid amounts.

Use for: overage billing (e.g., CDN with 5TB prepaid, then per-GB tiers above).

Algorithm:
1. Subtract prepaid amount from usage; if remaining <= 0, charge is zero
2. Build threshold list: configured thresholds + prepaid as the base tier
3. Sort thresholds by quantity descending
4. For each tier (highest to lowest):
   - Determine how much usage falls in this tier
   - Multiply tier usage by tier price
   - Subtract billed usage from remaining
5. Sum all tier charges; uses `MultipliedMoney` for sub-cent prices

## When to Use Which

| Scenario | Price Type |
|----------|-----------|
| Fixed monthly fee | SinglePrice |
| Per-unit with constant rate | SinglePrice |
| Overage above prepaid quota | ProgressivePrice |
| Commission/referral percentage | RatePrice |
| Exact quantity-to-price mapping | EnumPrice |

## AbstractPrice Rounding Rule

`AbstractPrice.calculateSum()` applies a minimum charge rule: if usage is nonzero but the calculated
sum rounds to zero (via `ROUND_HALF_UP`), it re-calculates with `ROUND_UP` to ensure at least 1 cent
is charged. This prevents free service for very small but nonzero usage.

## Price Interfaces

| Interface | Provides | Used by |
|-----------|----------|---------|
| `PriceInterface` | `calculateSum`, `calculateUsage`, `calculatePrice`, `isApplicable` | All prices |
| `PriceWithMoneyInterface` | `getPrice(): Money` | SinglePrice, ProgressivePrice |
| `PriceWithQuantityInterface` | `getPrepaid(): QuantityInterface` | SinglePrice, ProgressivePrice |
| `PriceWithRateInterface` | `getRate(): float` | RatePrice |
| `PriceWithSumsInterface` | `getSums(): Sums` | EnumPrice |
| `PriceWithThresholdsInterface` | `getThresholds(): ProgressivePriceThresholdList` | ProgressivePrice |
| `PriceWithCurrencyInterface` | `getCurrency(): Currency` | EnumPrice, via PriceWithMoneyInterface |
| `PriceWithUnitInterface` | `getUnit(): UnitInterface` | EnumPrice, via PriceWithQuantityInterface |
