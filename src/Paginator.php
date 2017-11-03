<?php

namespace Lampager;

use Lampager\Contracts\Cursor;
use Lampager\Query\Order;

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
     * @param  bool  $forward
     * @return $this
     */
    public function forward($forward = true)
    {
        $this->backward = !$forward;
        return $this;
    }

    /**
     * Define that the current pagination is going backward.
     *
     * @param  bool  $backward
     * @return $this
     */
    public function backward($backward = true)
    {
        $this->backward = $backward;
        return $this;
    }

    /**
     * Define that the cursor value is not included in the previous/next result.
     *
     * @param  bool  $exclusive
     * @return $this
     */
    public function exclusive($exclusive = true)
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    /**
     * Define that the cursor value is included in the previous/next result.
     *
     * @param  bool  $inclusive
     * @return $this
     */
    public function inclusive($inclusive = true)
    {
        $this->exclusive = !$inclusive;
        return $this;
    }

    /**
     * Define that the query can detect both "has_previous" and "has_next".
     *
     * @param  bool  $seekable
     * @return $this
     */
    public function seekable($seekable = true)
    {
        $this->seekable = $seekable;
        return $this;
    }

    /**
     * Define that the query can detect only either "has_previous" or "has_next".
     *
     * @param  bool  $unseekable
     * @return $this
     */
    public function unseekable($unseekable = true)
    {
        $this->seekable = !$unseekable;
        return $this;
    }

    /**
     * Define options from an associative array.
     *
     * @param  (bool|int|string[][])[]
     * @return $this
     */
    public function fromArray(array $options)
    {
        static $configurables = [
            'limit',
            'forward',
            'backward',
            'exclusive',
            'inclusive',
            'seekable',
            'unseekable',
        ];

        if (isset($options['orders'])) {
            foreach ($options['orders'] as $order) {
                $this->orderBy(...$order);
            }
        }

        foreach (array_intersect_key($options, array_flip($configurables)) as $key => $value) {
            $this->$key($value);
        }

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
