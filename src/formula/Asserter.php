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

use Hoa\Ruler\Context;
use Hoa\Ruler\Model;
use Hoa\Ruler\Ruler;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Asserter extends \Hoa\Ruler\Visitor\Asserter
{
    public function visitModel(Model $element, &$handle = null, $eldnah = null)
    {
        return $element->getExpression()->accept($this, $handle, $eldnah);
    }
}
