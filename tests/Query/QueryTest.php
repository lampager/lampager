<?php

namespace Lampager\Tests\Query;

use Lampager\ArrayCursor;
use Lampager\Exceptions\InvalidArgumentException;
use Lampager\Exceptions\Query\InsufficientConstraintsException;
use Lampager\Query\Direction;
use Lampager\Query\Order;
use Lampager\Query;
use Lampager\Query\SelectOrUnionAll;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase as BaseTestCase;

class QueryTest extends BaseTestCase
{
    #[Test]
    public function testConstruction(): void
    {
        $orders = [['updated_at', Order::ASC], ['created_at', Order::DESC], ['id', Order::ASC]];
        $cursor = new ArrayCursor(['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00']);
        $limit = 30;
        $backward = true;
        $exclusive = true;
        $seekable = true;
        $builder = 'Dummy Data';

        $query = Query::create($orders, $cursor, $limit, $backward, $exclusive, $seekable, $builder);

        $this->assertInstanceOf(SelectOrUnionAll::class, $query->selectOrUnionAll());
        $this->assertSame(['updated_at', Order::ASC], $query->orders()[0]->toArray());
        $this->assertSame(['created_at', Order::DESC], $query->orders()[1]->toArray());
        $this->assertSame(['id', Order::ASC], $query->orders()[2]->toArray());
        $this->assertSame($cursor, $query->cursor());
        $this->assertSame($limit, $query->limit());
        $this->assertSame(Direction::BACKWARD, (string)$query->direction());
        $this->assertTrue($query->backward());
        $this->assertFalse($query->forward());
        $this->assertSame($exclusive, $query->exclusive());
        $this->assertSame(!$exclusive, $query->inclusive());
        $this->assertSame($seekable, $query->seekable());
        $this->assertSame(!$seekable, $query->unseekable());
        $this->assertSame($builder, $query->builder());
    }

    #[Test]
    public function testDeepClone(): void
    {
        $orders = [['updated_at', Order::ASC], ['created_at', Order::DESC], ['id', Order::ASC]];
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];
        $limit = 30;
        $backward = true;
        $exclusive = true;
        $seekable = true;
        $builder = 'Dummy Data';

        $query = Query::create($orders, $cursor, $limit, $backward, $exclusive, $seekable, $builder);

        $cloneQuery = clone $query;
        $cloneOrders = $cloneQuery->orders();
        $cloneDirection = $cloneQuery->direction();

        $this->assertEquals($query->orders(), $cloneOrders);
        $this->assertNotSame($query->orders(), $cloneOrders);
        $this->assertEquals($query->direction(), $cloneDirection);
        $this->assertNotSame($query->direction(), $cloneDirection);
    }

    #[Test]
    public function testEmptyConstraints(): void
    {
        $this->expectException(InsufficientConstraintsException::class);
        $this->expectExceptionMessage('At least one order constraint required');

        $orders = [];
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];
        $limit = 30;
        $backward = true;
        $exclusive = true;
        $seekable = true;
        $builder = 'Dummy Data';

        Query::create($orders, $cursor, $limit, $backward, $exclusive, $seekable, $builder);
    }

    #[Test]
    public function testInvalidCursor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cursor must be either Cursor or array');

        $orders = [['updated_at', Order::ASC], ['created_at', Order::DESC], ['id', Order::ASC]];
        $cursor = 'Cursor';
        $limit = 30;
        $backward = true;
        $exclusive = true;
        $seekable = true;
        $builder = 'Dummy Data';

        Query::create($orders, $cursor, $limit, $backward, $exclusive, $seekable, $builder);
    }

    #[Test]
    public function testInvalidDirection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Direction must be bool');

        $orders = [['updated_at', Order::ASC], ['created_at', Order::DESC], ['id', Order::ASC]];
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];
        $limit = 30;
        $backward = null;
        $exclusive = true;
        $seekable = true;
        $builder = 'Dummy Data';

        Query::create($orders, $cursor, $limit, $backward, $exclusive, $seekable, $builder);
    }

    #[Test]
    public function testInvalidExclusive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Exclusive must be bool');

        $orders = [['updated_at', Order::ASC], ['created_at', Order::DESC], ['id', Order::ASC]];
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];
        $limit = 30;
        $backward = true;
        $exclusive = null;
        $seekable = true;
        $builder = 'Dummy Data';

        Query::create($orders, $cursor, $limit, $backward, $exclusive, $seekable, $builder);
    }

    #[Test]
    public function testInvalidSeekable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Seekable must be bool');

        $orders = [['updated_at', Order::ASC], ['created_at', Order::DESC], ['id', Order::ASC]];
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];
        $limit = 30;
        $backward = true;
        $exclusive = true;
        $seekable = null;
        $builder = 'Dummy Data';

        Query::create($orders, $cursor, $limit, $backward, $exclusive, $seekable, $builder);
    }
}
