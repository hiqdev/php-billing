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
use Hoa\Ruler\Model;
use Hoa\Ruler\Ruler;
use Hoa\Visitor\Visit;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class FormulaEngine
{
    /**
     * @var Ruler
     */
    protected $ruler;

    /**
     * @var Visit|Asserter
     */
    protected $asserter;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ChargeModifier
     */
    protected $discount;

    /**
     * @var ChargeModifier
     */
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
        try {
            $model = $this->interpret($formula);
            $result = $this->getRuler()->assert($model, $this->getContext());
        } catch (FormulaEngineException $e) {
            throw $e;
        } catch (\Hoa\Ruler\Exception\Asserter $e) {
            throw FormulaRuntimeError::fromException($e, $formula);
        } catch (\Exception $exception) {
            throw FormulaRuntimeError::create($formula, 'Formula run failed');
        }

        if (!$result instanceof ChargeModifier) {
            throw FormulaRuntimeError::create($formula, 'Formula run returned unexpected result');
        }

        return $result;
    }

    /**
     * @param string $formula
     * @return Model\Model
     * @throws
     */
    public function interpret(string $formula): Model\Model
    {
        try {
            return $this->getRuler()->interpret($this->normalize($formula));
        } catch (\Hoa\Compiler\Exception\Exception $exception) {
            throw FormulaSyntaxError::fromException($exception, $formula);
        } catch (\Hoa\Ruler\Exception\Interpreter $exception) {
            throw FormulaSyntaxError::fromException($exception, $formula);
        } catch (\Throwable $exception) {
            throw FormulaSyntaxError::create($formula);
        }
    }

    public function normalize(string $formula): string
    {
        return implode(' AND ', array_filter(array_map(function ($value) {
            $value = trim($value);
            if (strlen($value) === 0) {
                return null;
            }

            return $value;
        }, explode("\n", $formula))));
    }

    /**
     * Validates $formula
     *
     * @param string $formula
     * @return null|string `null` when formula has no errors or string error message
     */
    public function validate(string $formula): ?string
    {
        try {
            $this->build($formula);
            return null;
        } catch (FormulaEngineException $e) {
            return $e->getMessage();
        }
    }

    public function getRuler(): Ruler
    {
        if ($this->ruler === null) {
            $this->ruler = new Ruler();
            $this->ruler->setAsserter($this->getAsserter());
        }

        return $this->ruler;
    }

    public function getAsserter(): Visit
    {
        if ($this->asserter === null) {
            $this->asserter = new Asserter();
        }

        return $this->asserter;
    }

    public function getContext(): Context
    {
        if ($this->context === null) {
            $this->context = $this->buildContext();
        }

        return $this->context;
    }

    protected function buildContext(): Context
    {
        $context = new Context();
        $context['discount'] = $this->getDiscount();
        $context['leasing'] = $this->getLeasing();

        return $context;
    }

    public function getDiscount(): ChargeModifier
    {
        if ($this->discount === null) {
            $this->discount = new Discount();
        }

        return $this->discount;
    }

    public function getLeasing(): ChargeModifier
    {
        if ($this->leasing === null) {
            $this->leasing = new Leasing();
        }

        return $this->leasing;
    }
}
