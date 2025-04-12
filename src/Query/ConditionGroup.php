<?php

namespace Lampager\Query;

use Lampager\Contracts\Cursor;
use Lampager\ArrayCursor;
use Lampager\Exceptions\Query\CursorParameterException;

/**
 * Class ConditionGroup
 *
 * Conditions are concatenated with "AND".
 */
class ConditionGroup implements \IteratorAggregate
{
    /**
     * @var Condition[]
     */
    protected $conditions = [];

    /**
     * @param  Order[]               $orders
     * @param  Cursor|int[]|string[] $cursor
     * @param  Direction             $direction
     * @param  bool                  $exclusive
     * @param  bool                  $isSupportQuery
     * @return static[]
     */
    public static function createMany(array $orders, $cursor, Direction $direction, $exclusive, $isSupportQuery = false)
    {
        $groups = [];
        $count = count($orders);
        for ($i = 0; $i < $count; ++$i) {
            /*
             * Slice orders for building conditions.
             *
             * e.g.
             *
             *    1st:  updated_at = ? AND created_at = ? AND id > ?
             *    2nd:  updated_at = ? AND created_at > ?
             *    3rd:  updated_at > ?
             */
            $groups[] = static::create(
                array_slice($orders, 0, $count - $i),
                $cursor,
                $direction,
                $exclusive,
                $i === 0, // First row has a primary key
                $isSupportQuery
            );
        }
        return $groups;
    }

    /**
     * @param  Order[]               $orders
     * @param  Cursor|int[]|string[] $cursor
     * @param  Direction             $direction
     * @param  bool                  $exclusive
     * @param  bool                  $hasPrimaryKey
     * @param  bool                  $isSupportQuery
     * @return static
     */
    public static function create(array $orders, $cursor, Direction $direction, $exclusive, $hasPrimaryKey, $isSupportQuery = false)
    {
        $conditions = [];
        $i = 0;
        $count = count($orders);
        $cursor = $cursor instanceof Cursor ? $cursor : new ArrayCursor($cursor);
        foreach ($orders as $order) {
            if (!$cursor->has($order->column())) {
                // All parameters must be specified.
                throw new CursorParameterException("Missing cursor parameter: {$order->column()}", $order->column());
            }
            $isLastKey = ++$i === $count;
            $conditions[] = Condition::create(
                $order,
                $cursor->get($order->column()),
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
     * @return \ArrayIterator|Condition[]
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->conditions);
    }
}
