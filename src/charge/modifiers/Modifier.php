<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

use DateTimeImmutable;
use Exception;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\AddonsContainerInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\ChargeModifier;
use hiqdev\php\billing\charge\TimeLimitedModifierInterface;
use hiqdev\php\billing\formula\FormulaRuntimeError;

/**
 * Fixed discount.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Modifier implements ChargeModifier, AddonsContainerInterface, TimeLimitedModifierInterface
{
    use \hiqdev\php\billing\charge\modifiers\addons\WithReason;
    use \hiqdev\php\billing\charge\modifiers\addons\WithSince;
    use \hiqdev\php\billing\charge\modifiers\addons\WithTill;
    use \hiqdev\php\billing\charge\modifiers\addons\WithTerm;
    use \hiqdev\php\billing\charge\modifiers\addons\WithChargeType;

    /**
     * @var AddonInterface[]
     */
    protected $addons;

    public function __construct(array $addons = [])
    {
        $this->addons = $addons;
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        throw new Exception('not finished modifier');
    }

    public function isSuitable(?ChargeInterface $charge, ActionInterface $action): bool
    {
        $month = $action->getTime()->modify('first day of this month midnight');

        $since = $this->getSince();
        if ($since && $since->getValue() > $month) {
            return false;
        }

        return true;
    }

    public function discount()
    {
        return new Discount($this->addons);
    }

    public function cap()
    {
        return new Cap($this->addons);
    }

    public function installment()
    {
        return new Installment($this->addons);
    }

    public function getNext()
    {
        return new static($this->addons);
    }

    public function addAddon(string $name, AddonInterface $addon)
    {
        if ($this->hasAddon($name)) {
            throw new Exception("'$name' is already set");
        }
        $res = $this->getNext();
        $res->addons[$name] = $addon;

        return $res;
    }

    public function replaceAddon(string $name, AddonInterface $addon)
    {
        if (!$this->hasAddon($name)) {
            throw new Exception("Addong '$name' is not set");
        }

        $res = $this->getNext();
        $res->addons[$name] = $addon;

        return $res;
    }

    public function getAddon(string $name): ?AddonInterface
    {
        return empty($this->addons[$name]) ? null : $this->addons[$name];
    }

    public function hasAddon(string $name): bool
    {
        return isset($this->addons[$name]);
    }

    public function checkPeriod(DateTimeImmutable $time): bool
    {
        $since = $this->getSince();
        if ($since && $since->getValue() > $time) {
            return false;
        }

        $till = $this->getTill();
        if ($till && $till->getValue() <= $time) {
            return false;
        }

        $term = $this->getTerm();
        if ($term) {
            if (!$since) {
                throw new FormulaRuntimeError('since must be set to use term');
            }
            if ($term->countPeriodsPassed($since->getValue(), $time) >= 1) {
                return false;
            }
        }

        return true;
    }
}
