<?php

namespace Lampager\Tests;

use Lampager\Paginator;
use Lampager\Query\Order;
use Lampager\Query;
use PHPUnit\Framework\Attributes\Test;

class PaginatorTest extends TestCase
{
    #[Test]
    public function testOrderBy(): void
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

    #[Test]
    public function testLimit(): void
    {
        $paginator = new Paginator();
        $this->assertSame(15, $paginator->limit);

        $paginator->limit(20);
        $this->assertSame(20, $paginator->limit);
    }

    #[Test]
    public function testDirection(): void
    {
        $paginator = new Paginator();
        $this->assertFalse($paginator->backward);

        $paginator->backward();
        $this->assertTrue($paginator->backward);

        $paginator->backward(false);
        $this->assertFalse($paginator->backward);

        $paginator->forward();
        $this->assertFalse($paginator->backward);

        $paginator->forward(false);
        $this->assertTrue($paginator->backward);
    }

    #[Test]
    public function testInclusiveOrExclusive(): void
    {
        $paginator = new Paginator();
        $this->assertFalse($paginator->exclusive);

        $paginator->exclusive();
        $this->assertTrue($paginator->exclusive);

        $paginator->exclusive(false);
        $this->assertFalse($paginator->exclusive);

        $paginator->inclusive();
        $this->assertFalse($paginator->exclusive);

        $paginator->inclusive(false);
        $this->assertTrue($paginator->exclusive);
    }

    #[Test]
    public function testSeekableOrUnseekable(): void
    {
        $paginator = new Paginator();
        $this->assertFalse($paginator->seekable);

        $paginator->seekable();
        $this->assertTrue($paginator->seekable);

        $paginator->seekable(false);
        $this->assertFalse($paginator->seekable);

        $paginator->unseekable();
        $this->assertFalse($paginator->seekable);

        $paginator->unseekable(false);
        $this->assertTrue($paginator->seekable);
    }

    #[Test]
    public function testFromArray(): void
    {
        $paginator = new Paginator();
        $paginator->fromArray([
            'limit' => 20,
            'orders' => [
                ['foo', Order::DESC],
                ['baz'],
                ['bar', Order::DESC],
            ],
        ]);

        $this->assertSame(20, $paginator->limit);
        $this->assertSame([
            ['foo', Order::DESC],
            ['baz', Order::ASC],
            ['bar', Order::DESC],
        ], $paginator->orders);

        $paginator->fromArray([
            'forward' => true,
            'inclusive' => true,
            'unseekable' => true,
        ]);

        $this->assertFalse($paginator->backward);
        $this->assertFalse($paginator->exclusive);
        $this->assertFalse($paginator->seekable);

        $paginator->fromArray([
            'backward' => true,
            'exclusive' => true,
            'seekable' => true,
        ]);

        $this->assertTrue($paginator->backward);
        $this->assertTrue($paginator->exclusive);
        $this->assertTrue($paginator->seekable);
    }

    #[Test]
    public function testConfigure(): void
    {
        $paginator = new Paginator();
        $this->assertInstanceOf(Query::class, $paginator->orderBy('id')->configure(['id' => 1]));
    }
}
