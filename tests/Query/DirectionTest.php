<?php

namespace Lampager\Tests\Query;

use Lampager\Query\Direction;
use PHPUnit\Framework\TestCase as BaseTestCase;

class DirectionTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testForward()
    {
        $direction = new Direction(Direction::FORWARD);
        $this->assertForward($direction);
        $this->assertBackward($direction->inverse());
    }

    /**
     * @test
     */
    public function testBackward()
    {
        $direction = new Direction(Direction::BACKWARD);
        $this->assertBackward($direction);
        $this->assertForward($direction->inverse());
    }

    protected function assertForward(Direction $direction)
    {
        $this->assertTrue($direction->forward());
        $this->assertFalse($direction->backward());
        $this->assertSame(Direction::FORWARD, (string)$direction);
    }

    protected function assertBackward(Direction $direction)
    {
        $this->assertTrue($direction->backward());
        $this->assertFalse($direction->forward());
        $this->assertSame(Direction::BACKWARD, (string)$direction);
    }

    /**
     * @test
     */
    public function testInverseIsCloned()
    {
        $direction = new Direction('forward');
        $this->assertNotSame($direction, $direction->inverse());
    }

    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessage Direction must be "forward" or "backward"
     */
    public function testInvalidDirection()
    {
        new Direction('forword');
    }
}
