<?php

namespace Lampager\Tests\Query;

use Lampager\Query\Direction;
use Lampager\Query\Limit;
use Lampager\Query\Order;
use Lampager\Query\Select;
use Lampager\Query\SelectOrUnionAll;
use Lampager\Query\UnionAll;
use PHPUnit\Framework\TestCase as BaseTestCase;

class SelectOrUnionAllTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testAlwaysCreateSelectForEmptyCursor()
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::FORWARD);
        $cursor = [];

        foreach ([true, false] as $seekable) {
            $select = SelectOrUnionAll::create($orders, $cursor, new Limit(10), $direction, false, $seekable);
            $this->assertInstanceOf(Select::class, $select);
        }
    }

    /**
     * @test
     */
    public function testCreateSelectWhenUnseekable()
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::FORWARD);
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $select = SelectOrUnionAll::create($orders, $cursor, new Limit(10), $direction, false, false);
        $this->assertInstanceOf(Select::class, $select);
    }

    /**
     * @test
     */
    public function testCreateUnionAllWhenSeekable()
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::FORWARD);
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $unionAll = SelectOrUnionAll::create($orders, $cursor, new Limit(10), $direction, false, true);
        $this->assertInstanceOf(UnionAll::class, $unionAll);
    }

    /**
     * @test
     */
    public function testOrderInvertedOnBackward()
    {
        foreach ([Order::ASC, Order::DESC] as $order) {
            $sourceOrders = Order::createMany([['updated_at', $order], ['created_at', $order], ['id', $order]]);
            $direction = new Direction(Direction::BACKWARD);
            $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

            $select = SelectOrUnionAll::create($sourceOrders, $cursor, new Limit(10), $direction, false, false);
            foreach ($select->orders() as $i => $destOrder) {
                $this->assertNotSame($sourceOrders[$i]->order(), $destOrder);
            }
        }
    }

    /**
     * @test
     */
    public function testOrderNotInvertedOnForward()
    {
        foreach ([Order::ASC, Order::DESC] as $order) {
            $sourceOrders = Order::createMany([['updated_at', $order], ['created_at', $order], ['id', $order]]);
            $direction = new Direction(Direction::FORWARD);
            $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

            $select = SelectOrUnionAll::create($sourceOrders, $cursor, new Limit(10), $direction, false, false);
            foreach ($select->orders() as $i => $destOrder) {
                $this->assertSame($sourceOrders[$i]->order(), $destOrder->order());
            }
        }
    }

    /**
     * @test
     */
    public function testSelectIteratesItself()
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::FORWARD);
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $select = SelectOrUnionAll::create($orders, $cursor, new Limit(10), $direction, false, false);
        $it = $select->getIterator();
        $this->assertSame($select, $it->current());
        $it->next();
        $this->assertFalse($it->valid());
    }

    /**
     * @test
     */
    public function testUnionAllIteratesSupportAndMainQueriesInThisOrder()
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::FORWARD);
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $unionAll = SelectOrUnionAll::create($orders, $cursor, new Limit(10), $direction, false, true);
        $it = $unionAll->getIterator();
        $this->assertSame($unionAll->mainQuery(), $it->current());
        $it->next();
        $this->assertSame($unionAll->supportQuery(), $it->current());
        $it->next();
        $this->assertFalse($it->valid());
    }

    /**
     * @test
     */
    public function testSupportQueryInvertedOnUnionAll()
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::FORWARD);
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $unionAll = SelectOrUnionAll::create($orders, $cursor, new Limit(10), $direction, false, true);
        $mainQuery = $unionAll->mainQuery();
        $supportQuery = $unionAll->supportQuery();

        $this->assertNotSame($mainQuery, $supportQuery);
        $this->assertNotSame($mainQuery->orders()[0], $supportQuery->orders()[0]);
        $this->assertSame($mainQuery->limit()->original(), $supportQuery->limit()->original());
        $this->assertNotSame($mainQuery->limit()->toInteger(), $supportQuery->limit()->toInteger());
        $this->assertSame($mainQuery->where()[0]->conditions()[0]->comparator(), $supportQuery->where()[0]->conditions()[0]->comparator());
        $this->assertSame($mainQuery->where()[0]->conditions()[1]->comparator(), $supportQuery->where()[0]->conditions()[1]->comparator());
        $this->assertNotSame($mainQuery->where()[0]->conditions()[2]->comparator(), $supportQuery->where()[0]->conditions()[2]->comparator());
    }

    /**
     * @test
     */
    public function testDeepClone()
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::FORWARD);
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $unionAll = SelectOrUnionAll::create($orders, $cursor, new Limit(10), $direction, false, true);
        $mainQuery = $unionAll->mainQuery();
        $supportQuery = $unionAll->supportQuery();

        $cloneUnionAll = clone $unionAll;
        $cloneMainQuery = $cloneUnionAll->mainQuery();
        $cloneSupportQuery = $cloneUnionAll->supportQuery();

        $this->assertNotSame($mainQuery, $cloneMainQuery);
        $this->assertNotSame($mainQuery->orders()[0], $cloneMainQuery->orders()[0]);
        $this->assertSame($mainQuery->orders()[0]->order(), $cloneMainQuery->orders()[0]->order());

        $this->assertNotSame($mainQuery->limit(), $cloneMainQuery->limit());
        $this->assertSame($mainQuery->limit()->original(), $cloneMainQuery->limit()->original());
        $this->assertSame($mainQuery->limit()->toInteger(), $cloneMainQuery->limit()->toInteger());

        $this->assertNotSame($mainQuery->where(), $cloneMainQuery->where());
        $this->assertSame($mainQuery->where()[0]->conditions()[0]->comparator(), $cloneMainQuery->where()[0]->conditions()[0]->comparator());

        $this->assertNotSame($supportQuery, $cloneSupportQuery);
        $this->assertNotSame($supportQuery->orders()[0], $cloneSupportQuery->orders()[0]);
        $this->assertSame($supportQuery->orders()[0]->order(), $cloneSupportQuery->orders()[0]->order());

        $this->assertNotSame($supportQuery->limit(), $cloneSupportQuery->limit());
        $this->assertSame($supportQuery->limit()->original(), $cloneSupportQuery->limit()->original());
        $this->assertSame($supportQuery->limit()->toInteger(), $cloneSupportQuery->limit()->toInteger());

        $this->assertNotSame($supportQuery->where(), $cloneSupportQuery->where());
        $this->assertSame($supportQuery->where()[0]->conditions()[0]->comparator(), $cloneSupportQuery->where()[0]->conditions()[0]->comparator());
    }
}
