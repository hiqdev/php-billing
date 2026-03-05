# Billing Domain Model

## Entity Pipeline

```
Customer → Plan → Price → Sale
                            ↓
                         Action → Calculator → Charge → Aggregator → Bill
```

## Core Entities

**Customer** — the billable party. Has a seller (reseller hierarchy).

**Plan** — a tariff/pricing plan. Contains a collection of Prices.
Prices are immutable after assignment (`CannotReassignException`).

**Price** — a billing rule. Applied when `isApplicable(action)` returns true.
Matching logic: `action.target.matches(price.target) AND action.type.matches(price.type)`.

**Sale** — a subscription binding Customer → Target → Plan. Has optional `closeTime`.
Can have null Plan (for one-time sales). Represents "customer X uses resource Y under plan Z".

**Action** — a metered activity. The only thing that gets charged.
Has: type, target, quantity, customer, time, optional sale, optional parent, fractionOfMonth.

**Charge** — result of matching an Action to a Price. Holds: used quantity (usage),
calculated money (sum), reference to the Price that created it, optional parent charge.

**Bill** — aggregation of Charges. Represents an invoice line item. Immutable once created.

## Matching Constants

- `Target::ANY` (null) — matches any target
- `Target::NONE` (INF) — matches no target
- `Type::ANY` (null) — matches any type
- `Type::NONE` (INF) — matches no type

## Immutability Rules

Once set, these fields cannot be reassigned (throws `CannotReassignException`):

- Plan → prices
- Price → plan
- Action → sale
- Sale → id
- Charge → id, parent

**Rationale:** billing history integrity. To update a tariff, create a new Plan with new ID.

## Execution Flow

1. **Calculator** iterates Actions, finds applicable Prices via `isApplicable()`
2. Each Price produces one or more **Charges** with calculated sum
3. **Aggregator** groups Charges by billing criteria (customer, type, target, period)
4. Grouped Charges become **Bills** (invoice line items)

## Money

Money is a value object — never use floats for monetary values.
Uses `hiqdev\php\units` for quantity handling.
