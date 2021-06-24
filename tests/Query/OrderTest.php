<?php

namespace Lampager\Tests\Query;

use Lampager\Query\Order;
use PHPUnit\Framework\TestCase as BaseTestCase;

class OrderTest extends BaseTestCase
{
    /**
     * @test
     */
    public function testCreateMany()
    {
        $orders = Order::createMany([['created_at', Order::ASC], ['id', Order::DESC]]);

        $this->assertSame('created_at', $orders[0]->column());
        $this->assertSame(Order::ASC, $orders[0]->order());
        $this->assertSame(['created_at', Order::ASC], $orders[0]->toArray());
        $this->assertTrue($orders[0]->ascending());
        $this->assertFalse($orders[0]->descending());

        $this->assertSame('id', $orders[1]->column());
        $this->assertSame(Order::DESC, $orders[1]->order());
        $this->assertSame(['id', Order::DESC], $orders[1]->toArray());
        $this->assertTrue($orders[1]->descending());
        $this->assertFalse($orders[1]->ascending());
    }

    /**
     * @test
     */
    public function testInverse()
    {
        $original = new Order('created_at', Order::ASC);
        $inverse = $original->inverse();

        $this->assertSame('created_at', $inverse->column());
        $this->assertSame(Order::DESC, $inverse->order());
        $this->assertSame(['created_at', Order::DESC], $inverse->toArray());
        $this->assertTrue($inverse->descending());
        $this->assertFalse($inverse->ascending());

        $this->assertNotSame($original, $inverse);
    }

    /**
     * @test
     */
    public function testInvalidDirection()
    {
        $this->expectException(\Lampager\Exceptions\Query\BadKeywordException::class);
        $this->expectExceptionMessage('Order must be "asc", "ascending", "desc" or "descending"');

        new Order('id', 'ascccending');
    }
}
