<?php

namespace Lampager\Tests\Query;

use Lampager\Exceptions\Query\BadKeywordException;
use Lampager\Query\Direction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase as BaseTestCase;

class DirectionTest extends BaseTestCase
{
    #[Test]
    public function testForward(): void
    {
        $direction = new Direction(Direction::FORWARD);
        $this->assertForward($direction);
        $this->assertBackward($direction->inverse());
    }

    #[Test]
    public function testBackward(): void
    {
        $direction = new Direction(Direction::BACKWARD);
        $this->assertBackward($direction);
        $this->assertForward($direction->inverse());
    }

    #[Test]
    protected function assertForward(Direction $direction): void
    {
        $this->assertTrue($direction->forward());
        $this->assertFalse($direction->backward());
        $this->assertSame(Direction::FORWARD, (string)$direction);
    }

    #[Test]
    protected function assertBackward(Direction $direction): void
    {
        $this->assertTrue($direction->backward());
        $this->assertFalse($direction->forward());
        $this->assertSame(Direction::BACKWARD, (string)$direction);
    }

    #[Test]
    public function testInverseIsCloned(): void
    {
        $direction = new Direction('forward');
        $this->assertNotSame($direction, $direction->inverse());
    }

    #[Test]
    public function testInvalidDirection(): void
    {
        $this->expectException(BadKeywordException::class);
        $this->expectExceptionMessage('Direction must be "forward" or "backward"');

        new Direction('forword');
    }
}
