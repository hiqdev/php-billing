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
use hiqdev\php\billing\charge\modifiers\Discount;
use hiqdev\php\billing\charge\modifiers\Leasing;
use Hoa\Ruler\Context;
use Hoa\Ruler\Ruler;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class FormulaEngine
{
    protected $ruler;

    protected $asserter;

    protected $context;

    protected $discount;

    protected $leasing;

    public function __construct()
    {
        if (!class_exists(Context::class)) {
            throw new \Exception('to use formula engine install `hoa/ruler`');
        }
    }

    /**
     * @param string $formula
     * @return ChargeModifier
     */
    public function build(string $formula): ChargeModifier
    {
        return $this->getRuler()->assert($formula, $this->getContext());
    }

    public function getRuler()
    {
        if ($this->ruler === null) {
            $this->ruler = new Ruler();
            $this->ruler->setAsserter($this->getAsserter());
        }

        return $this->ruler;
    }

    public function getAsserter()
    {
        if ($this->asserter === null) {
            $this->asserter = new Asserter();
        }

        return $this->asserter;
    }

    public function getContext()
    {
        if ($this->context === null) {
            $this->context = $this->buildContext();
        }

        return $this->context;
    }

    protected function buildContext()
    {
        $context = new Context();
        $context['discount'] = $this->getDiscount();
        $context['leasing'] = $this->getLeasing();

        return $context;
    }

    public function getDiscount()
    {
        if ($this->discount === null) {
            $this->discount = new Discount();
        }

        return $this->discount;
    }

    public function getLeasing()
    {
        if ($this->leasing === null) {
            $this->leasing = new Leasing();
        }

        return $this->leasing;
    }
}
