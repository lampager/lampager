<?php

namespace Lampager\Query;

use Lampager\Contracts\Cursor;

/**
 * Class SelectOrUnionAll
 */
abstract class SelectOrUnionAll implements \IteratorAggregate
{
    /**
     * @param  Order[]                $orders
     * @param  Cursor|int[]|string[]  $cursor
     * @param  Limit                  $limit
     * @param  Direction              $direction
     * @param  bool                   $exclusive
     * @param  bool                   $seekable
     * @return Select|static|UnionAll
     */
    public static function create(array $orders, $cursor, Limit $limit, Direction $direction, $exclusive, $seekable)
    {
        $mainQuery = new Select(
            $cursor ? ConditionGroup::createMany($orders, $cursor, $direction, $exclusive) : [],
            $direction->backward()
                ? array_map(static function (Order $order) {
                    return $order->inverse();
                }, $orders)
                : $orders,
            $limit
        );

        if (!$cursor || !$seekable) {
            // We don't need UNION ALL and support query when cursor parameters are empty,
            // or it does not need to be seekable.
            return $mainQuery;
        }

        return new UnionAll($mainQuery);
    }
}
