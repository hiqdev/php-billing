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

use hiqdev\php\billing\charge\modifiers\LastCombination;
use Hoa\Ruler\Model;
use Hoa\Ruler\Context;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Asserter extends \Hoa\Ruler\Visitor\Asserter
{
    /**
     * @param Context $context Context.
     */
    public function __construct(Context $context = null)
    {
        if (null !== $context) {
            $this->setContext($context);
        }

        $this->setOperator('and', [$this, 'makeAnd']);
    }

    public function makeAnd($lhs, $rhs) {
        return new LastCombination($lhs, $rhs);
    }

    public function visitModel(Model $element, &$handle = null, $eldnah = null)
    {
        return $element->getExpression()->accept($this, $handle, $eldnah);
    }
}
