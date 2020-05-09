<?php

declare(strict_types=1);
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\event;

use League\Event\EventInterface;

/**
 * Trait EventTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait EventAwareTrait
{
    /**
     * @var EventInterface[]
     */
    private $events = [];

    /**
     * Registers that $event occurred
     */
    public function recordThat(EventInterface $event): void
    {
        $this->events[] = $event;
    }

    /**
     * @return EventInterface[] of occurred events
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
