<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\formula;

use hiqdev\php\billing\charge\ChargeModifier;
use Hoa\Ruler\Model\Model;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface FormulaEngineInterface
{
    public function build(string $formula): ChargeModifier;

    /**
     * Validates $formula.
     *
     * @return string|null `null` when formula has no errors or string error message
     */
    public function validate(string $formula): ?string;

    /**
     * @throws FormulaSyntaxError
     */
    public function interpret(string $formula): Model;
}
