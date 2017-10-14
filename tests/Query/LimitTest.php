<?php

namespace Lampager\Tests\Query;

use Lampager\Query\Limit;
use PHPUnit\Framework\TestCase as BaseTestCase;

class LimitTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testMainQuery()
    {
        $limit = new Limit(10);
        $this->assertMainQuery($limit);
        $this->assertSupportQuery($limit->inverse());
    }

    /**
     * @test
     */
    public function testSupportQuery()
    {
        $limit = new Limit(10, true);
        $this->assertSupportQuery($limit);
        $this->assertMainQuery($limit->inverse());
    }

    protected function assertMainQuery(Limit $limit)
    {
        $intValue = $limit->toInteger();
        $original = $limit->original();

        $this->assertTrue($limit->isMainQuery());
        $this->assertFalse($limit->isSupportQuery());
        $this->assertSame($original + 1, $intValue);
    }

    protected function assertSupportQuery(Limit $limit)
    {
        $intValue = $limit->toInteger();

        $this->assertTrue($limit->isSupportQuery());
        $this->assertFalse($limit->isMainQuery());
        $this->assertSame(1, $intValue);
    }

    /**
     * @test
     */
    public function testInverseIsCloned()
    {
        $limit = new Limit(10);
        $this->assertNotSame($limit, $limit->inverse());
    }

    /**
     * @test
     * @expectedException \DomainException
     * @expectedExceptionMessage Limit must be integer
     */
    public function testInvalidLimitType()
    {
        new Limit('foo');
    }

    /**
     * @test
     * @expectedException \OutOfRangeException
     * @expectedExceptionMessage Limit must be positive integer
     */
    public function testInvalidLimitRange()
    {
        new Limit(0);
    }
}
