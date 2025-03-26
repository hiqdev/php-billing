<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product;

use hiqdev\billing\registry\behavior\Behavior;

class DummyBehavior extends Behavior
{
    private $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}
