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

use Hoa\Ruler\Model;
use Hoa\Ruler\Model\Bag\Context;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Asserter extends \Hoa\Ruler\Visitor\Asserter
{
    public function visitModel(Model $element, &$handle = null, $eldnah = null)
    {
        return $element->getExpression()->accept($this, $handle, $eldnah);
    }

    /**
     * Visit a method dimension.
     *
     * @param   mixed &$contextPointer Pointer to the current context.
     * @param   array $dimension Dimension bucket.
     * @param   int $dimensionNumber Dimension number.
     * @param   string $elementId Element name.
     * @param   mixed &$handle Handle (reference).
     * @param   mixed $eldnah Handle (not reference).
     * @return  void
     * @throws \Hoa\Ruler\Exception\Asserter
     */
    protected function visitMethodDimension(
        &$contextPointer,
        array $dimension,
        $dimensionNumber,
        $elementId,
        &$handle = null,
        $eldnah  = null
    ) {
        $value  = $dimension[Context::ACCESS_VALUE];
        $method = $value->getName();

        if (!is_object($contextPointer)) {
            throw new \Hoa\Ruler\Exception\Asserter(
                'Try to call an undefined method: %s ' .
                '(dimension number %d of %s), because it is ' .
                'not an object.',
                7,
                [$method, $dimensionNumber, $elementId]
            );
        }

        $arguments = [];

        foreach ($value->getArguments() as $argument) {
            $arguments[] = $argument->accept($this, $handle, $eldnah);
        }

        $contextPointer = call_user_func_array(
            [$contextPointer, $method],
            $arguments
        );

        return;
    }
}
