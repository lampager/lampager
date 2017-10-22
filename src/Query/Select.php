<?php

namespace Lampager\Query;

/**
 * Class Select
 */
class Select extends SelectOrUnionAll
{
    /**
     * @var null|Where
     */
    protected $where;

    /**
     * @var Order[]
     */
    protected $orders;

    /**
     * @var Limit
     */
    protected $limit;

    /**
     * Select constructor.
     *
     * @param null|Where $where
     * @param Order[]    $orders
     * @param Limit      $limit
     */
    public function __construct($where, array $orders, Limit $limit)
    {
        $this->where = $where;
        $this->orders = static::validate(...$orders);
        $this->limit = $limit;
    }

    /**
     * @param  Order[] ...$orders
     * @return Order[]
     */
    protected static function validate(Order ...$orders)
    {
        return $orders;
    }

    /**
     * @return null|Where
     */
    public function where()
    {
        return $this->where;
    }

    /**
     * @return Order[]
     */
    public function orders()
    {
        return $this->orders;
    }

    /**
     * @return Limit
     */
    public function limit()
    {
        return $this->limit;
    }

    /**
     * @return static
     */
    public function inverse()
    {
        return new static(
            $this->where ? $this->where->inverse() : null,
            array_map(static function (Order $order) {
                return $order->inverse();
            }, $this->orders),
            $this->limit->inverse()
        );
    }

    /**
     * Clone Select.
     */
    public function __clone()
    {
        if ($this->where) {
            $this->where = clone $this->where;
        }
        $this->orders = array_map(static function (Order $order) {
            return clone $order;
        }, $this->orders);
        $this->limit = clone $this->limit;
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \ArrayIterator|Select[]
     */
    public function getIterator()
    {
        return new \ArrayIterator([$this]);
    }
}
