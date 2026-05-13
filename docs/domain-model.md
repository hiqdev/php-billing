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

### Calculator Pipeline

1. `findSales(order)` — matches Actions to Sales (direct or via repository)
2. `findPlans(order)` — resolves Plans from Sales (loads from repository if needed)
3. `calculatePlan(plan, action)` — iterates all Prices in the Plan
4. `calculatePrice(price, action)` — calls `calculateCharge()`, then applies `ChargeModifier` if price has one
5. `calculateCharge(price, action)` — core calculation:
   - Checks `action.isApplicable(price)` (target + type matching)
   - Checks sale time is not in the future
   - Calculates usage via `price.calculateUsage(quantity)`
   - Calculates sum via `price.calculateSum(quantity)`
   - Specializes type/target via Generalizer
   - Returns a Charge

### Generalizer

**Generalizer** (`src/charge/Generalizer.php`) maps Charges to Bills. It is the customization point
for downstream projects that need different aggregation behavior.

Key responsibilities:
- `createBill(charge)` — converts a Charge into a Bill (negates sum for accounting)
- `specializeType(priceType, actionType)` — resolves which Type to use on the Charge (base: returns price type)
- `specializeTarget(priceTarget, actionTarget)` — resolves which Target to use (base: returns price target)

### Aggregator

**Aggregator** groups Charges into Bills using `Bill.getUniqueString()` as the aggregation key.

Bill unique key composition:
```
currency + customer.uniqueId + target.uniqueId + type.uniqueId + time (ISO 8601)
```

Bills with the same key are merged: sums are added, quantities are added (if same unit), charge arrays are concatenated.

## Money and Units

Money is a value object — never use floats for monetary values.
Uses `hiqdev\php\units` for quantity handling.
