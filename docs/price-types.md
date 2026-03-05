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

The most complex type:
1. Prepaid amount is subtracted from usage
2. Remaining usage is matched against progressive tiers
3. Each tier has its own rate

## When to Use Which

| Scenario | Price Type |
|----------|-----------|
| Fixed monthly fee | SinglePrice |
| Per-unit with constant rate | SinglePrice |
| Overage above prepaid quota | ProgressivePrice |
| Commission/referral percentage | RatePrice |
| Exact quantity-to-price mapping | EnumPrice |
