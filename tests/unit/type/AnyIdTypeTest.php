<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\type;

use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\billing\type\AnyIdType;
use PHPUnit\Framework\TestCase;

class AnyIdTypeTest extends TestCase
{
    public function testTypeWithAnyIdHasAnyId(): void
    {
        $type = new AnyIdType('monthly,leasing');

        $this->assertSame(TypeInterface::ANY, $type->getId());
        $this->assertSame('monthly,leasing', $type->getName());
    }
}
