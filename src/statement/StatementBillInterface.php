<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2021, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\statement;

use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use DateTimeImmutable;
use Money\Money;

/**
 * StatementBill Interface.
 *
 * @author Yurii Myronchuk <bladeroot@gmail.com>
 */
interface StatementBillInterface extends BillInterface
{
    public function getMonth(): DateTimeImmutable;

    public function getFrom(): ?string;

    public function getPrice(): ?Money;

    public function getOveruse(): ?Money;

    public function getPrepaid(): ?QuantityInterface;

    public function getTariffType(): ?TypeInterface;
}
