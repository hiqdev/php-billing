<?php

declare(strict_types=1);

namespace hiqdev\php\billing\bill;

/**
 * BillTxn
 *
 * Represents an immutable external payment transaction identifier.
 *
 * A TransactionId uniquely identifies a financial transaction
 * as defined by an external payment or accounting system
 * (e.g. Business Central, merchant gateways, banks).
 *
 * This value:
 * - Is created by an external system, never generated internally
 * - Is stable across retries, webhooks, and re-imports
 * - Is used for idempotency and reconciliation
 * - Has meaning outside of the database and application boundaries
 *
 * Uniqueness is guaranteed only within the scope of a source system
 * (see Source / external system).
 *
 * Typical examples:
 * - UUIDs
 * - Gateway transaction references
 * - Accounting document numbers
 */
class BillTxn
{
    /** @var int|string|null */
    protected $value;

    public function __construct($txn = null)
    {
        $this->value = $txn;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function fromString(string $txn): self
    {
        return new self($txn);
    }
}
