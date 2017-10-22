<?php

namespace Lampager\Query;

use Lampager\Query\Conditions\Group;

/**
 * Class Where
 *
 * Conditions are concatenated with "OR".
 */
class Where implements \IteratorAggregate
{
    /**
     * @var Group[]
     */
    protected $groups = [];

    /**
     * @param  Order[]        $orders
     * @param  int[]|string[] $cursor
     * @param  Direction      $direction
     * @param  bool           $exclusive
     * @param  bool           $isSupportQuery
     * @return static
     */
    public static function create(array $orders, array $cursor, Direction $direction, $exclusive, $isSupportQuery = false)
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
            $groups[] = Group::create(
                array_slice($orders, 0, $count - $i),
                $cursor,
                $direction,
                $exclusive,
                $i === 0, // First row has a primary key
                $isSupportQuery
            );
        }
        return new static($groups);
    }

    /**
     * Where constructor.
     *
     * @param Group[] $groups
     */
    public function __construct(array $groups)
    {
        $this->groups = static::validate(...$groups);
    }

    /**
     * @param  Group[] $groups
     * @return Group[]
     */
    protected static function validate(Group ...$groups)
    {
        return $groups;
    }

    /**
     * @return Group[]
     */
    public function groups()
    {
        return $this->groups;
    }

    /**
     * @return static
     */
    public function inverse()
    {
        return new static(array_map(static function (Group $group) {
            return $group->inverse();
        }, $this->groups));
    }

    /**
     * Clone Where.
     */
    public function __clone()
    {
        $this->groups = array_map(static function (Group $group) {
            return clone $group;
        }, $this->groups);
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \ArrayIterator|Group[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->groups);
    }
}
