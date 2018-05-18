<?php
declare(strict_types=1);

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
use hiqdev\php\billing\charge\modifiers\addons\Reason;
use hiqdev\php\billing\charge\modifiers\addons\Since;
use hiqdev\php\billing\charge\modifiers\addons\Till;

/**
 * Fixed discount.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Modifier implements ChargeModifier
{
    /**
     * @var AddonInterface[]
     */
    protected $addons;

    /**
     * @var string[]|AddonInterface[]
     */
    protected $supportedAddons = [];

    public function __construct(array $addons = [])
    {
        $this->addons = $addons;

        $this->supportedAddons = [
            'reason' => Reason::class,
            'since' => Since::class,
            'till' => Till::class
        ];
    }

    public function __call($name, $params)
    {
        if (strncmp($name, 'get', 3) === 0) {
            return $this->getAddon(strtolower(substr($name, 3)));
        }
        if (isset($this->supportedAddons[$name])) {
            return $this->addAddon($name, new $this->supportedAddons[$name](...$params));
        }

        throw new \BadMethodCallException("Method \"$name\" is not supported");
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        throw new \Exception('not finished modifier');
    }

    public function addAddon($name, $addon)
    {
        if (!isset($this->supportedAddons[$name])) {
            throw new \Exception("Addon \"$name\" is not supported by this class");
        }
        if (isset($this->addons[$name])) {
            throw new \Exception("Addon \"$name\" is already set");
        }
        $this->addons[$name] = $addon;

        return $this;
    }

    public function getAddon(string $name): ?AddonInterface
    {
        return $this->addons[$name] ?? null;
    }

    public function hasAddon(string $name): bool
    {
        return isset($this->addons[$name]);
    }


    public function discount()
    {
        return new Discount($this->addons);
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
