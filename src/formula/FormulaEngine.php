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

use Exception;
use hiqdev\php\billing\charge\ChargeModifier;
use hiqdev\php\billing\charge\modifiers\Cap;
use hiqdev\php\billing\charge\modifiers\Discount;
use hiqdev\php\billing\charge\modifiers\Increase;
use hiqdev\php\billing\charge\modifiers\Installment;
use hiqdev\php\billing\charge\modifiers\Once;
use Hoa\Ruler\Context;
use Hoa\Ruler\Model\Model;
use Hoa\Ruler\Ruler;
use Hoa\Visitor\Visit;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class FormulaEngine implements FormulaEngineInterface
{
    public const FORMULAS_SEPARATOR = "\n";

    protected ?Ruler $ruler = null;

    protected ?Visit $asserter = null;

    protected ?Context $context = null;

    protected ?ChargeModifier $discount = null;

    protected ?ChargeModifier $installment = null;

    protected ?ChargeModifier $increase = null;

    protected ?Cap $cap = null;

    protected ?Once $once = null;

    public function __construct(
        private CacheInterface $cache
    ) {
        if (!class_exists(Context::class)) {
            throw new Exception('to use formula engine install `hoa/ruler`');
        }
    }

    public function build(string $formula): ChargeModifier
    {
        try {
            $model = $this->interpret($formula);
            $result = $this->getRuler()->assert($model, $this->getContext());
        } catch (FormulaSemanticsError $e) {
            throw FormulaSemanticsError::fromException($e, $formula);
        } catch (FormulaEngineException $e) {
            throw $e;
        } catch (\Hoa\Ruler\Exception\Asserter $e) {
            throw FormulaRuntimeError::fromException($e, $formula);
        } catch (\Exception $e) {
            throw FormulaRuntimeError::fromException($e, $formula, 'Formula run failed');
        }

        if (!$result instanceof ChargeModifier) {
            throw FormulaRuntimeError::create($formula, 'Formula run returned unexpected result');
        }

        return $result;
    }

    /**
     * @throws FormulaEngineException
     */
    public function interpret(string $formula): Model
    {
        try {
            $normalize = $this->normalize($formula);
            if ($normalize === null) {
                $normalize = '';
            }
            $rule = str_replace(self::FORMULAS_SEPARATOR, ' AND ', $normalize);

            $key = md5(__METHOD__ . $rule);
            $model = $this->cache->get($key);
            if ($model === null) {
                $model = $this->getRuler()->interpret($rule);
                $this->cache->set($key, $model);
            }

            return $model;
        } catch (\Hoa\Compiler\Exception\Exception $exception) {
            throw FormulaSyntaxError::fromException($exception, $formula);
        } catch (\Hoa\Ruler\Exception\Interpreter $exception) {
            throw FormulaSyntaxError::fromException($exception, $formula);
        } catch (\Throwable $exception) {
            throw FormulaSyntaxError::create($formula, 'Failed to interpret formula: ' . $exception->getMessage());
        }
    }

    public function normalize(string $formula): ?string
    {
        $lines = explode(self::FORMULAS_SEPARATOR, $formula);
        $normalized = array_map(function ($value) {
            $value = trim($value);
            if ('' === $value) {
                return null;
            }

            return $value;
        }, $lines);
        $cleared = array_filter($normalized);

        return empty($cleared) ? null : implode(self::FORMULAS_SEPARATOR, $cleared);
    }

    /**
     * Validates $formula.
     *
     * @return string|null `null` when formula has no errors or string error message
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

    public function setAsserter(Visit $asserter): self
    {
        $this->asserter = $asserter;
        if ($this->ruler !== null) {
            $this->ruler->setAsserter($asserter);
        }

        return $this;
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
        $context['installment'] = $this->getInstallment();
        $context['increase'] = $this->getIncrease();
        $context['cap'] = $this->getCap();
        $context['once'] = $this->getOnce();

        return $context;
    }

    public function getDiscount(): ChargeModifier
    {
        if ($this->discount === null) {
            $this->discount = new Discount();
        }

        return $this->discount;
    }

    public function getInstallment(): ChargeModifier
    {
        if ($this->installment === null) {
            $this->installment = new Installment();
        }

        return $this->installment;
    }

    public function getIncrease(): ChargeModifier
    {
        if ($this->increase === null) {
            $this->increase = new Increase();
        }

        return $this->increase;
    }

    private function getCap(): ChargeModifier
    {
        if ($this->cap === null) {
            $this->cap = new Cap();
        }

        return $this->cap;
    }

    private function getOnce(): ChargeModifier
    {
        if ($this->once === null) {
            $this->once = new Once();
        }

        return $this->once;
    }

    public function __clone(): void
    {
        if ($this->context !== null) {
            $this->context = clone $this->context;
        }
        if ($this->ruler !== null) {
            $this->ruler = clone $this->ruler;
        }
        if ($this->asserter !== null) {
            $this->asserter = clone $this->asserter;
        }
    }
}
