<?php

namespace Lampager\Tests\Query;

use Lampager\Exceptions\Query\LimitParameterException;
use Lampager\Query\Limit;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase as BaseTestCase;

class LimitTest extends BaseTestCase
{
    #[Test]
    public function testMainQuery(): void
    {
        $limit = new Limit(10);
        $this->assertMainQuery($limit);
        $this->assertSupportQuery($limit->inverse());
    }

    #[Test]
    public function testSupportQuery(): void
    {
        $limit = new Limit(10, true);
        $this->assertSupportQuery($limit);
        $this->assertMainQuery($limit->inverse());
    }

    #[Test]
    protected function assertMainQuery(Limit $limit): void
    {
        $intValue = $limit->toInteger();
        $original = $limit->original();

        $this->assertTrue($limit->isMainQuery());
        $this->assertFalse($limit->isSupportQuery());
        $this->assertSame($original + 1, $intValue);
    }

    #[Test]
    protected function assertSupportQuery(Limit $limit): void
    {
        $intValue = $limit->toInteger();

        $this->assertTrue($limit->isSupportQuery());
        $this->assertFalse($limit->isMainQuery());
        $this->assertSame(1, $intValue);
    }

    #[Test]
    public function testInverseIsCloned(): void
    {
        $limit = new Limit(10);
        $this->assertNotSame($limit, $limit->inverse());
    }

    #[Test]
    public function testInvalidLimitType(): void
    {
        $this->expectException(LimitParameterException::class);
        $this->expectExceptionMessage('Limit must be integer');

        new Limit('foo');
    }

    #[Test]
    public function testInvalidLimitRange(): void
    {
        $this->expectException(LimitParameterException::class);
        $this->expectExceptionMessage('Limit must be positive integer');

        new Limit(0);
    }
}
