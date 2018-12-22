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

use hiqdev\php\billing\charge\modifiers\AddonInterface;

/**
 * Charge type addon
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ChargeType implements AddonInterface
{
    /**
     * @var string
     */
    protected $value;

    public function __construct($value)
    {
        $this->value = $this->ensureValidValue($value);
    }

    public function getValue()
    {
        return $this->value;
    }

    private function ensureValidValue($value): string
    {
        if (!preg_match('/^[\w_]+$/', $value)) {
            throw new \InvalidArgumentException('Charge type is not valid');
        }

        return (string) $value;
    }
}
