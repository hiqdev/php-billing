<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\quantity;

interface QuantityFormatterInterface
{
    /**
     * Returns textual user friendly representation of the quantity.
     * E.g. 20 days, 30 GB, 1 year.
     *
     * @return string
     */
    public function format(): string;

    /**
     * Returns numeric to be saved in DB.
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Returns numeric user friendly representation of the quantity.
     *
     * @return string
     */
    public function getClientValue(): string;
}
