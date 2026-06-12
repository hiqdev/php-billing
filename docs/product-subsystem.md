# Product Subsystem

The `src/product/` namespace provides a higher-level registry for describing **what products exist**, **how they are priced**, and **how their charges are rendered on invoices**. It sits above the core billing pipeline and is used to configure the system at boot time.

## Concepts at a Glance

```
BillingRegistry
  └── TariffTypeDefinition[]
        ├── TariffTypeInterface        (identifies the tariff type)
        ├── PriceTypeDefinitionCollection
        │     └── PriceTypeDefinition[]  (price line items + their document representations)
        └── TariffTypeBehaviorRegistry
              └── BehaviorCollection[]   (optional billing behaviors)
```

## BillingRegistry

`BillingRegistry` (`src/product/BillingRegistry.php`) is the root registry. It holds a list of `TariffTypeDefinition` objects and supports locking once configuration is complete.

```php
$registry = new BillingRegistry();
$registry->addTariffType($definition);
$registry->lock(); // freezes configuration
```

After locking, all contained definitions are also locked — no further mutation is possible. This enforces immutability of billing configuration at runtime.

`priceTypes()` yields all `PriceTypeDefinition` instances across all registered tariff types.

## TariffTypeDefinition

`TariffTypeDefinition` (`src/product/TariffTypeDefinition.php`) describes a single tariff type: its price lines and optional behaviors.

```php
$definition = (new TariffTypeDefinition($tariffType))
    ->ofProduct($product)
    ->withPrices()
        ->add(...)
        ->end()
    ->withBehaviors()
        ->attach(new SomeBehavior())
        ->end()
    ->end();
```

- `withPrices()` — returns a `PriceTypeDefinitionCollection` for chaining price definitions
- `withBehaviors()` — returns the behavior registry for attaching `BehaviorInterface` instances
- `end()` — validates that at least one price type is defined and returns the definition
- Requires a product (`ofProduct()`) before calling `end()`

## PriceTypeDefinition

`PriceTypeDefinition` (`src/product/price/PriceTypeDefinition.php`) describes a single price line within a tariff type. It is associated with a billing `Type`, a unit, and a document representation.

`PriceTypeStorage` provides a lookup service for finding `PriceTypeDefinition` instances by type name.

`InvoiceDescriptionsBuilder` (`src/product/InvoiceDescriptionsBuilder.php`) iterates all registered price types and collects their `documentRepresentation()` values — used to generate invoice line descriptions.

## Behavior System

Behaviors (`src/product/behavior/`) attach optional billing logic to tariff types or individual price type definitions.

**`BehaviorInterface`** — implement this to define a custom behavior. Each behavior knows its tariff type (set automatically on `attach()`).

**`BehaviorCollection`** — abstract iterable container of `BehaviorInterface` instances. Extend it to create typed collections.

**`TariffTypeBehaviorRegistry`** — manages behaviors for a `TariffTypeDefinition`. Provides:
- `hasBehavior(string $class)` — checks whether a behavior of the given class is attached
- `findBehaviorByClass(string $class)` — retrieves a specific behavior instance

Example use: a `ProrationBehavior` that signals the tariff type supports pro-rated monthly charges.

## Invoice Representation

`Representation` (`src/product/invoice/Representation.php`) is an abstract class that associates a charge `Type` with a SQL snippet used to render that charge on an invoice. Extend it to create concrete representations:

```php
class ServerTrafficRepresentation extends Representation
{
    public function __construct()
    {
        parent::__construct('SELECT ... FROM traffic WHERE type = :type');
    }
}
```

`RepresentationCollection` holds multiple representations and guards against duplicates via `RepresentationUniquenessGuard`.

## Quantity Formatters

`QuantityFormatterInterface` (`src/product/quantity/`) formats a `QuantityInterface` value for human display.

`QuantityFormatterDefinition` binds a formatter class to a unit type. `QuantityFormatterFactory` creates formatter instances from definitions. `QuantityFormatterNotFoundException` is thrown when no formatter is registered for a given unit.

## Lock Mechanism

All major product objects use the `HasLock` trait. Once `lock()` is called:
- `ensureNotLocked()` throws if any further mutation is attempted
- Child objects are also locked transitively

This guarantees that billing configuration is frozen before any billing runs occur.
