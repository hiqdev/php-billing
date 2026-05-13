<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\behavior;

use hiqdev\php\billing\product\behavior\Behavior;

class TestBehavior extends Behavior
{
    public function __construct(private $context)
    {
    }

    public function getContext()
    {
        return $this->context;
    }

    public function description(): string
    {
        return 'Test behavior for testing purposes';
    }
}
