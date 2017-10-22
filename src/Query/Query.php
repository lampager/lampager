<?php

namespace Lampager\Query;

use Lampager\Contracts\Cursor;
use Lampager\ArrayCursor;

/**
 * Class Query
 */
class Query
{
    /**
     * @var Select|SelectOrUnionAll|UnionAll
     */
    protected $selectOrUnionAll;

    /**
     * @var Order[]
     */
    protected $orders;

    /**
     * @var Cursor
     */
    protected $cursor;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var Direction
     */
    protected $direction;

    /**
     * @var bool
     */
    protected $exclusive;

    /**
     * @var bool
     */
    protected $seekable;

    /**
     * @var mixed
     */
    protected $builder;

    /**
     * @param  string[][]            $orders
     * @param  Cursor|int[]|string[] $cursor
     * @param  int                   $limit
     * @param  bool                  $backward
     * @param  bool                  $exclusive
     * @param  bool                  $seekable
     * @param  mixed                 $builder
     * @return static
     */
    public static function create(array $orders, $cursor, $limit, $backward, $exclusive, $seekable, $builder = null)
    {
        if (!$orders) {
            throw new \OutOfRangeException('At least one order constraint required');
        }
        $direction = new Direction($backward ? Direction::BACKWARD : Direction::FORWARD);
        $orders = Order::createMany($orders);
        $limit = new Limit($limit);
        $selectOrUnionAll = SelectOrUnionAll::create($orders, $cursor, $limit, $direction, $exclusive, $seekable);
        return new static($selectOrUnionAll, $orders, $cursor, $limit, $direction, $exclusive, $seekable, $builder);
    }

    /**
     * Query constructor.
     *
     * @param Select|SelectOrUnionAll|UnionAll $selectOrUnionAll
     * @param Order[]                          $orders
     * @param Cursor|int[]|string[]            $cursor
     * @param Limit                            $limit
     * @param Direction                        $direction
     * @param bool                             $exclusive
     * @param bool                             $seekable
     * @param mixed                            $builder
     */
    public function __construct(SelectOrUnionAll $selectOrUnionAll, array $orders, $cursor, Limit $limit, Direction $direction, $exclusive, $seekable, $builder = null)
    {
        $this->selectOrUnionAll = $selectOrUnionAll;
        $this->orders = $orders;
        $this->cursor = $cursor instanceof Cursor ? $cursor : new ArrayCursor($cursor);
        $this->limit = $limit->original();
        $this->direction = $direction;
        $this->exclusive = $exclusive;
        $this->seekable = $seekable;
        $this->builder = $builder;
    }

    /**
     * @return Select|Select[]|SelectOrUnionAll|UnionAll
     */
    public function selectOrUnionAll()
    {
        return $this->selectOrUnionAll;
    }

    /**
     * @return Order[]
     */
    public function orders()
    {
        return $this->orders;
    }

    /**
     * @return Cursor
     */
    public function cursor()
    {
        return $this->cursor;
    }

    /**
     * @return int
     */
    public function limit()
    {
        return $this->limit;
    }

    /**
     * @return Direction
     */
    public function direction()
    {
        return $this->direction;
    }

    /**
     * @return bool
     */
    public function forward()
    {
        return $this->direction()->forward();
    }

    /**
     * @return bool
     */
    public function backward()
    {
        return $this->direction()->backward();
    }

    /**
     * @return bool
     */
    public function exclusive()
    {
        return $this->exclusive;
    }

    /**
     * @return bool
     */
    public function inclusive()
    {
        return !$this->exclusive;
    }

    /**
     * @return bool
     */
    public function seekable()
    {
        return $this->seekable;
    }

    /**
     * @return bool
     */
    public function unseekable()
    {
        return !$this->seekable;
    }

    /**
     * @return mixed
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * Clone Query.
     */
    public function __clone()
    {
        $this->selectOrUnionAll = clone $this->selectOrUnionAll;
        $this->orders = array_map(static function (Order $order) {
            return clone $order;
        }, $this->orders);
        $this->direction = clone $this->direction;
    }
}
