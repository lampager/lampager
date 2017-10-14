<?php

namespace Lampager\Query\Conditions;

use Lampager\Query\Direction;
use Lampager\Query\Order;

/**
 * Class Group
 *
 * Conditions are concatenated with "AND".
 */
class Group implements \IteratorAggregate
{
    /**
     * @var Condition[]
     */
    protected $conditions = [];

    /**
     * @param  Order[]        $orders
     * @param  int[]|string[] $cursor
     * @param  Direction      $direction
     * @param  bool           $exclusive
     * @param  bool           $hasPrimaryKey
     * @param  bool           $isSupportQuery
     * @return static
     */
    public static function create(array $orders, array $cursor, Direction $direction, $exclusive, $hasPrimaryKey, $isSupportQuery)
    {
        $conditions = [];
        $i = 0;
        $count = count($orders);
        foreach ($orders as $order) {
            if (!isset($cursor[$order->column()])) {
                // All parameters must be specified.
                throw new \UnexpectedValueException("Missing cursor parameter: {$order->column()}");
            }
            $isLastKey = ++$i === $count;
            $conditions[] = Condition::create(
                $order,
                $cursor[$order->column()],
                $direction,
                $exclusive,
                $isLastKey && $hasPrimaryKey, // When it is the last key and we have a primary key, it is also the primary key.
                $isLastKey,
                $isSupportQuery
            );
        }
        return new static($conditions);
    }

    /**
     * Group constructor.
     *
     * @param Condition[] $conditions
     */
    public function __construct(array $conditions)
    {
        $this->conditions = static::validate(...$conditions);
    }

    /**
     * @param  Condition[] $conditions
     * @return Condition[]
     */
    protected static function validate(Condition ...$conditions)
    {
        return $conditions;
    }

    /**
     * @return Condition[]
     */
    public function conditions()
    {
        return $this->conditions;
    }

    /**
     * @return static
     */
    public function inverse()
    {
        return new static(array_map(static function (Condition $condition) {
            return $condition->inverse();
        }, $this->conditions));
    }

    /**
     * Clone Group.
     */
    public function __clone()
    {
        $this->conditions = array_map(static function (Condition $condition) {
            return clone $condition;
        }, $this->conditions);
    }

    /**
     * Retrieve an external iterator.
     *
     * @return Condition[]|\Generator
     */
    public function getIterator()
    {
        foreach ($this->conditions as $i => $condition) {
            yield $i => $condition;
        }
    }
}
