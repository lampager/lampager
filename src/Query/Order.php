<?php

namespace Lampager\Query;

/**
 * Class Order
 */
class Order
{
    const ASCENDING = 'asc';
    const DESCENDING = 'desc';
    const ASC = 'asc';
    const DESC = 'desc';

    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $order;

    /**
     * @param  string[][] $orders
     * @return Order[]
     */
    public static function createMany(array $orders)
    {
        return array_map(static function (array $order) {
            return new Order(...$order);
        }, $orders);
    }

    /**
     * Order constructor.
     *
     * @param string $column
     * @param string $order
     */
    public function __construct($column, $order)
    {
        $this->column = (string)$column;
        $this->order = static::validate($order);
    }

    /**
     * @param $order
     * @return string
     */
    protected static function validate($order)
    {
        $order = strtolower($order);
        if (!preg_match('/\A(asc|desc)(?:ending)?\z/', $order, $m)) {
            throw new \DomainException('Order must be "asc", "ascending", "desc" or "descending"');
        }
        return $m[1];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [$this->column, $this->order];
    }

    /**
     * @return string
     */
    public function column()
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function order()
    {
        return $this->order;
    }

    /**
     * @return bool
     */
    public function ascending()
    {
        return $this->order === static::ASCENDING;
    }

    /**
     * @return bool
     */
    public function descending()
    {
        return $this->order === static::DESCENDING;
    }

    /**
     * @return static
     */
    public function inverse()
    {
        return new static($this->column, $this->order === static::ASCENDING ? static::DESCENDING : static::ASCENDING);
    }
}
