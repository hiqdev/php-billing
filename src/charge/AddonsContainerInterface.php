<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\charge\modifiers\AddonInterface;

/**
 * Interface AddonsContainerInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface AddonsContainerInterface
{
    /**
     * Adds the $addon into the container
     *
     * @param string $name
     * @param AddonInterface $addon
     * @return ChargeModifier|AddonsContainerInterface
     * @throw Exception when the addon $name already exists in the container
     */
    public function addAddon(string $name, AddonInterface $addon);

    /**
     * Gets addon by name
     *
     * @param $name
     * @return AddonInterface|null – the addon or `null` when not exists in the container
     */
    public function getAddon(string $name): ?AddonInterface;

    /**
     * @param string $name
     * @return bool whether addon is in the container
     */
    public function hasAddon(string $name): bool;
}
