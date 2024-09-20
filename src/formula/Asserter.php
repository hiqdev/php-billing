<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\formula;

use hiqdev\php\billing\charge\modifiers\FullCombination;
use Hoa\Ruler\Context;
use Hoa\Ruler\Model\Model;
use Hoa\Ruler\Model\Operator;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Asserter extends \Hoa\Ruler\Visitor\Asserter
{
    /**
     * @param Context $context context
     */
    public function __construct(Context $context = null)
    {
        parent::__construct($context);
        $this->setOperator('and', [$this, 'makeAnd']);
    }

    public function makeAnd($lhs, $rhs)
    {
        return new FullCombination($lhs, $rhs);
    }

    public function visitModel(Model $element, &$handle = null, $eldnah = null)
    {
        return $this->getExpression($element)->accept($this, $handle, $eldnah);
    }

    private function getExpression(Model $element): Operator
    {
        return $element->getExpression();
    }
}
