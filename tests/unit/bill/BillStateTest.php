<?php

namespace hiqdev\php\billing\tests\unit\bill;

use hiqdev\php\billing\bill\BillState;
use PHPUnit\Framework\TestCase;

class BillStateTest extends TestCase
{
    public function testGetName()
    {
        $billState = BillState::new();
        $this->assertEquals(BillState::STATE_NEW, $billState->getName());

        $billState = BillState::finished();
        $this->assertEquals(BillState::STATE_FINISHED, $billState->getName());
    }

    public function testIsNew()
    {
        $billState = BillState::new();
        $this->assertTrue($billState->isNew());
        $this->assertFalse($billState->isFinished());

        $billState = BillState::finished();
        $this->assertFalse($billState->isNew());
    }

    public function testIsFinished()
    {
        $billState = BillState::finished();
        $this->assertTrue($billState->isFinished());
        $this->assertFalse($billState->isNew());

        $billState = BillState::new();
        $this->assertFalse($billState->isFinished());
    }

    public function testNew()
    {
        $billState = BillState::new();
        $this->assertInstanceOf(BillState::class, $billState);
        $this->assertEquals(BillState::STATE_NEW, $billState->getName());
    }

    public function testFinished()
    {
        $billState = BillState::finished();
        $this->assertInstanceOf(BillState::class, $billState);
        $this->assertEquals(BillState::STATE_FINISHED, $billState->getName());
    }

    public function testFromString()
    {
        $billState = BillState::fromString(BillState::STATE_NEW);
        $this->assertEquals(BillState::STATE_NEW, $billState->getName());

        $billState = BillState::fromString(BillState::STATE_FINISHED);
        $this->assertEquals(BillState::STATE_FINISHED, $billState->getName());
    }

    public function testFromStringWithInvalidState()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("wrong bill state 'invalid'");

        BillState::fromString('invalid');
    }

    /**
     * Helper method to access protected property
     */
    private function getStateProperty(BillState $billState)
    {
        $reflection = new \ReflectionClass($billState);
        $property = $reflection->getProperty('state');
        $property->setAccessible(true);

        return $property->getValue($billState);
    }
}
