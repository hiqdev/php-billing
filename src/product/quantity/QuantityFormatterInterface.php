<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\quantity;

interface QuantityFormatterInterface
{
    /**
     * Returns textual user friendly representation of the quantity.
     * E.g. 20 days, 30 GB, 1 year.
     */
    public function format(): string;

    /**
     * Returns numeric to be saved in DB.
     */
    public function getValue(): string;

    /**
     * Returns numeric user friendly representation of the quantity.
     */
    public function getClientValue(): string;
}
