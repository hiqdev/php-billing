<?php
declare(strict_types=1);

namespace hiqdev\php\billing\usage;

use hiqdev\php\billing\Exception\RuntimeException;

interface UsageRecorderInterface
{
    /**
     * @throws RuntimeException when unable to record the passed usage
     */
    public function record(Usage $usage): void;
}
