<?php

namespace Lampager\Tests;

use Lampager\Paginator;
use Lampager\Query\Order;
use Lampager\Query\Query;
use PHPUnit\Framework\TestCase as BaseTestCase;

class PaginatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testOrderBy()
    {
        $paginator = new Paginator();
        $paginator->orderByDesc('foo');
        $paginator->orderBy('baz');
        $paginator->orderBy('bar', Order::DESC);

        $this->assertSame([
            ['foo', Order::DESC],
            ['baz', Order::ASC],
            ['bar', Order::DESC],
        ], $paginator->orders);

        $paginator->clearOrderBy();

        $this->assertSame([], $paginator->orders, 'orders');
    }

    /**
     * @test
     */
    public function testLimit()
    {
        $paginator = new Paginator();
        $this->assertSame(15, $paginator->limit);

        $paginator->limit(20);
        $this->assertSame(20, $paginator->limit);
    }

    /**
     * @test
     */
    public function testDirection()
    {
        $paginator = new Paginator();
        $this->assertFalse($paginator->backward);

        $paginator->backward();
        $this->assertTrue($paginator->backward);

        $paginator->forward();
        $this->assertFalse($paginator->backward);
    }

    /**
     * @test
     */
    public function testInclusiveOrExclusive()
    {
        $paginator = new Paginator();
        $this->assertFalse($paginator->exclusive);

        $paginator->exclusive();
        $this->assertTrue($paginator->exclusive);

        $paginator->inclusive();
        $this->assertFalse($paginator->exclusive);
    }

    /**
     * @test
     */
    public function testSeekableOrUnseekable()
    {
        $paginator = new Paginator();
        $this->assertFalse($paginator->seekable);

        $paginator->seekable();
        $this->assertTrue($paginator->seekable);

        $paginator->unseekable();
        $this->assertFalse($paginator->seekable);
    }

    /**
     * @test
     */
    public function testConfigure()
    {
        $paginator = new Paginator();
        $this->assertInstanceOf(Query::class, $paginator->orderBy('id')->configure(['id' => 1]));
    }
}
