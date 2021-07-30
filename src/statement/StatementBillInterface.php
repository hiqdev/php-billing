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
use DateTimeImmutable;

/**
 * StatementBill Interface.
 *
 * @author Yurii Myronchuk <bladeroot@gmail.com>
 */
interface StatementBillInterface extends BillInterface
{
    public function getMonth(): DateTimeImmutable;

    public function getFrom(): string;
}
