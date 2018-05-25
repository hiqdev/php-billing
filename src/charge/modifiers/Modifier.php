<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\ChargeModifier;

/**
 * Fixed discount.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Modifier implements ChargeModifier
{
    use \hiqdev\php\billing\charge\modifiers\addons\WithReason;
    use \hiqdev\php\billing\charge\modifiers\addons\WithSince;
    use \hiqdev\php\billing\charge\modifiers\addons\WithTill;
    use \hiqdev\php\billing\charge\modifiers\addons\WithTerm;

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
        throw new \Exception('not finished modifier');
    }

    public function isSuitable(?ChargeInterface $charge, ActionInterface $action): bool
    {
        $month = $action->getTime()->modify('first day of this month midnight');

        return $this->checkPeriod($month);
    }

    public function discount()
    {
        return new Discount($this->addons);
    }

    public function leasing()
    {
        return new Leasing($this->addons);
    }

    public function addAddon($name, $addon)
    {
        if (isset($this->addons[$name])) {
            throw new \Exception("'$name' is already set");
        }
        $res = $this->getNext();
        $res->addons[$name] = $addon;

        return $res;
    }

    public function getNext()
    {
        return new static($this->addons);
    }

    public function getAddon($name)
    {
        return empty($this->addons[$name]) ? null : $this->addons[$name];
    }

    public function checkPeriod(DateTimeImmutable $time)
    {
        $since = $this->getSince();
        if ($since && $since->getValue() > $time) {
            return false;
        }

        $till = $this->getTill();
        if ($till && $till->getValue() <= $time) {
            return false;
        }

        return true;
    }
}
