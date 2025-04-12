<?php

namespace Lampager\Query;

/**
 * Class Select
 */
class Select extends SelectOrUnionAll
{
    /**
     * @var ConditionGroup[]
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
     * @param ConditionGroup[] $where
     * @param Order[]          $orders
     * @param Limit            $limit
     */
    public function __construct(array $where, array $orders, Limit $limit)
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
     * @return ConditionGroup[]
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
            array_map(static function (ConditionGroup $group) {
                return $group->inverse();
            }, $this->where),
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
        $this->where = array_map(static function (ConditionGroup $group) {
            return clone $group;
        }, $this->where);
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
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator([$this]);
    }
}
