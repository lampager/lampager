<?php

namespace Lampager\Query;

/**
 * Class SelectOrUnionAll
 */
abstract class SelectOrUnionAll implements \IteratorAggregate
{
    /**
     * @param  Order[]                $orders
     * @param  int[]|string[]         $cursor
     * @param  Limit                  $limit
     * @param  Direction              $direction
     * @param  bool                   $exclusive
     * @param  bool                   $seekable
     * @return Select|static|UnionAll
     */
    public static function create(array $orders, array $cursor, Limit $limit, Direction $direction, $exclusive, $seekable)
    {
        $mainQuery = new Select(
            $cursor ? Where::create($orders, $cursor, $direction, $exclusive) : null,
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
