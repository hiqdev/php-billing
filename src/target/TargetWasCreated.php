<?php
declare(strict_types=1);
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2021, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

use League\Event\AbstractEvent;

/**
 * Event TargetWasCreated occurs when a new target is being created
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class TargetWasCreated extends AbstractEvent
{
    private TargetInterface $target;

    public function __construct(TargetInterface $target)
    {
        $this->target = $target;
    }

    public function getTarget(): TargetInterface
    {
        return $this->target;
    }

    public static function occurred(TargetInterface $target): self
    {
        $self = new self($target);

        return $self;
    }
}
