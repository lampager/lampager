<?php

namespace Lampager\Tests\Query;

use Lampager\ArrayCursor;
use Lampager\Query\Direction;
use Lampager\Query\Order;
use Lampager\Query\Query;
use Lampager\Query\SelectOrUnionAll;
use PHPUnit\Framework\TestCase as BaseTestCase;

class QueryTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testConstruction()
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

    /**
     * @test
     */
    public function testDeepClone()
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

    /**
     * @test
     * @expectedException \OutOfRangeException
     * @expectedExceptionMessage At least one order constraint required
     */
    public function testEmptyConstraints()
    {
        $orders = [];
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];
        $limit = 30;
        $backward = true;
        $exclusive = true;
        $seekable = true;
        $builder = 'Dummy Data';

        Query::create($orders, $cursor, $limit, $backward, $exclusive, $seekable, $builder);
    }
}
