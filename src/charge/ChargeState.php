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

/**
 * Charge State.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ChargeState
{
    const STATE_NEW      = 'new';
    const STATE_FINISHED = 'finished';

    /** @var string */
    protected $state;

    private function __construct(string $state = self::STATE_NEW)
    {
        $this->state = $state;
    }

    public function getName()
    {
        return $this->state;
    }

    public function isNew()
    {
        return $this->state === self::STATE_NEW;
    }

    public function isFinished()
    {
        return $this->state === self::STATE_FINISHED;
    }

    public static function new()
    {
        return new self(self::STATE_NEW);
    }

    public static function finished()
    {
        return new self(self::STATE_FINISHED);
    }

    public static function fromString(string $name)
    {
        foreach ([self::STATE_NEW, self::STATE_FINISHED] as $state) {
            if ($state === $name) {
                return new self($state);
            }
        }

        throw new \Exception("wrong charge state '$name'");
    }
}
