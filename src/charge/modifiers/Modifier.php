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
    /**
     * @var AddonInterface[]
     */
    protected $addons;

    public function __construct(array $addons = [])
    {
        $this->addons = $addons;
    }

    public function modifyCharge(ChargeInterface $charge, ActionInterface $action): array
    {
        throw new \Exception('not finished modifier');
    }

    public function reason($text)
    {
        return $this->addAddon('reason', new Reason($text));
    }

    public function since($time)
    {
        return $this->addAddon('since', new Since($time));
    }

    public function till($time)
    {
        return $this->addAddon('till', new Till($time));
    }

    public function addAddon($name, $addon)
    {
        if (isset($this->addons[$name])) {
            throw new \Exception("'$name' is already set");
        }
        $this->addons[$name] = $addon;

        return $this;
    }
}
