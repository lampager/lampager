<?php

namespace Lampager;

use Lampager\Contracts\Cursor;
use Lampager\Query\Order;
use Lampager\Query\Query;

/**
 * Class Paginator
 */
class Paginator
{
    /**
     * @var string[][]
     */
    public $orders = [];

    /**
     * @var int
     */
    public $limit = 15;

    /**
     * @var bool
     */
    public $backward = false;

    /**
     * @var bool
     */
    public $exclusive = false;

    /**
     * @var bool
     */
    public $seekable = false;

    /**
     * @var mixed
     */
    public $builder;

    /**
     * Add cursor parameter name for ORDER BY statement.
     *
     * IMPORTANT: Last parameter MUST be a primary key
     *
     *    e.g.   $factory->orderBy('created_at')->orderBy('id') // <--- PRIMARY KEY
     *
     * @param  string      $column
     * @param  null|string $order
     * @return $this
     */
    public function orderBy($column, $order = Order::ASC)
    {
        $this->orders[] = [$column, $order];
        return $this;
    }

    /**
     * Add cursor parameter name for ORDER BY statement.
     *
     * @param  string $column
     * @return $this
     */
    public function orderByDesc($column)
    {
        return $this->orderBy($column, Order::DESC);
    }

    /**
     * Clear all cursor parameters.
     *
     * @return $this
     */
    public function clearOrderBy()
    {
        $this->orders = [];
        return $this;
    }

    /**
     * Define limit.
     *
     * @param  int   $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Define that the current pagination is going forward.
     *
     * @return $this
     */
    public function forward()
    {
        $this->backward = false;
        return $this;
    }

    /**
     * Define that the current pagination is going backward.
     *
     * @return $this
     */
    public function backward()
    {
        $this->backward = true;
        return $this;
    }

    /**
     * Define that the cursor value is not included in the previous/next result.
     *
     * @return $this
     */
    public function exclusive()
    {
        $this->exclusive = true;
        return $this;
    }

    /**
     * Define that the cursor value is included in the previous/next result.
     *
     * @return $this
     */
    public function inclusive()
    {
        $this->exclusive = false;
        return $this;
    }

    /**
     * Define that the query can detect both "has_previous" and "has_next".
     *
     * @return $this
     */
    public function seekable()
    {
        $this->seekable = true;
        return $this;
    }

    /**
     * Define that the query can detect only either "has_previous" or "has_next".
     *
     * @return $this
     */
    public function unseekable()
    {
        $this->seekable = false;
        return $this;
    }

    /**
     * Build Query instance.
     *
     * @param  Cursor|int[]|string[] $cursor
     * @return Query
     */
    public function configure($cursor = [])
    {
        return Query::create($this->orders, $cursor, $this->limit, $this->backward, $this->exclusive, $this->seekable, $this->builder);
    }
}
