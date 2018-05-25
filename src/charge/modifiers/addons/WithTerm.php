<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\addons;

/**
 * With Term trait.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
trait WithTerm
{
    public function lasts($term): self
    {
        return $this->addAddon('term', Period::fromString($term));
    }

    public function getTerm(): ?Period
    {
        return $this->getAddon('term');
    }
}
