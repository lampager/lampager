<?php

namespace Lampager\Tests\Query;

use Lampager\Exceptions\Query\CursorParameterException;
use Lampager\Query\Condition;
use Lampager\Query\ConditionGroup;
use Lampager\Query\Direction;
use Lampager\Query\Order;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase as BaseTestCase;

class WhereConditionsTest extends BaseTestCase
{
    /**
     * @param  Order[] $orders
     * @return Order[]
     */
    protected function inverseOrderArray(array $orders)
    {
        return array_map(static function (Order $order) {
            return $order->inverse();
        }, $orders);
    }

    /**
     * @param  ConditionGroup[] $groups
     * @return Condition[][]
     */
    protected function castWhereToArray(array $groups)
    {
        $r = [];
        foreach ($groups as $i => $group) {
            $this->assertSame($group, $groups[$i]);
            $conditions = $group->conditions();
            foreach ($group as $j => $condition) {
                $this->assertSame($condition, $conditions[$j]);
                $r[$i][$j] = $condition;
            }
        }
        return $r;
    }

    /**
     * @param Condition  $condition
     * @param string     $left
     * @param string     $comparator
     * @param int|string $right
     */
    protected function assertCondition(Condition $condition, $left, $comparator, $right)
    {
        $this->assertSame($left, $condition->left());
        $this->assertSame($comparator, $condition->comparator());
        $this->assertSame($right, $condition->right());
        $this->assertSame([$left, $comparator, $right], $condition->toArray());

        if ($condition->left() === 'id') {
            $this->assertTrue($condition->isPrimaryKey());
        } else {
            $this->assertFalse($condition->isPrimaryKey());
        }
    }

    #[Test]
    public function testAscendingForward(): void
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::FORWARD);
        $this->assertAscendingForwardOrDescendingBackwardInclusive($direction, $orders);
        $this->assertAscendingForwardOrDescendingBackwardExclusive($direction, $orders);
    }

    #[Test]
    public function testAscendingBackward(): void
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::BACKWARD);
        $this->assertAscendingBackwardOrDescendingForwardInclusive($direction, $orders);
        $this->assertAscendingBackwardOrDescendingForwardExclusive($direction, $orders);
    }

    #[Test]
    public function testDescendingForward(): void
    {
        $orders = Order::createMany([['updated_at', Order::DESC], ['created_at', Order::DESC], ['id', Order::DESC]]);
        $direction = new Direction(Direction::FORWARD);
        $this->assertAscendingBackwardOrDescendingForwardInclusive($direction, $orders);
        $this->assertAscendingBackwardOrDescendingForwardExclusive($direction, $orders);
    }

    #[Test]
    public function testDescendingBackward(): void
    {
        $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
        $direction = new Direction(Direction::FORWARD);
        $this->assertAscendingForwardOrDescendingBackwardInclusive($direction, $orders);
        $this->assertAscendingForwardOrDescendingBackwardExclusive($direction, $orders);
    }

    /**
     * Support: `updated_at` = ? AND `created_at` = ? AND `id` < ? OR
     *          `updated_at` = ? AND `created_at` < ? OR
     *          `updated_at` < ?
     *
     * Main:    `updated_at` = ? AND `created_at` = ? AND `id` >= ? OR
     *          `updated_at` = ? AND `created_at` > ? OR
     *          `updated_at` > ?
     *
     * @param Direction $direction
     * @param Order[]   $orders
     */
    protected function assertAscendingForwardOrDescendingBackwardInclusive(Direction $direction, array $orders)
    {
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $supportGroups = ConditionGroup::createMany($this->inverseOrderArray($orders), $cursor, $direction, false, true);

        $this->specify('support query assertion', function () use ($supportGroups) {
            $where = $this->castWhereToArray($supportGroups);

            $this->assertCondition($where[0][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[0][1], 'created_at', '=', '2017-01-01 12:00:00');
            $this->assertCondition($where[0][2], 'id', '<', 10);
            $this->assertArrayNotHasKey(3, $where[0]);

            $this->assertCondition($where[1][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[1][1], 'created_at', '<', '2017-01-01 12:00:00');
            $this->assertArrayNotHasKey(2, $where[1]);

            $this->assertCondition($where[2][0], 'updated_at', '<', '2017-01-01 18:00:00');
            $this->assertArrayNotHasKey(1, $where[2]);

            $this->assertArrayNotHasKey(3, $where);
        });

        $mainGroups = ConditionGroup::createMany($orders, $cursor, $direction, false);

        $this->specify('main query assertion', function () use ($mainGroups) {
            $where = $this->castWhereToArray($mainGroups);

            $this->assertCondition($where[0][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[0][1], 'created_at', '=', '2017-01-01 12:00:00');
            $this->assertCondition($where[0][2], 'id', '>=', 10);
            $this->assertArrayNotHasKey(3, $where[0]);

            $this->assertCondition($where[1][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[1][1], 'created_at', '>', '2017-01-01 12:00:00');
            $this->assertArrayNotHasKey(2, $where[1]);

            $this->assertCondition($where[2][0], 'updated_at', '>', '2017-01-01 18:00:00');
            $this->assertArrayNotHasKey(1, $where[2]);

            $this->assertArrayNotHasKey(3, $where);
        });

        $this->specify('manually constructed inverse condition groups are equivalent to auto-generated ones', function () use ($mainGroups, $supportGroups) {
            $this->assertEquals($mainGroups, array_map(static function (ConditionGroup $group) {
                return clone $group->inverse();
            }, $supportGroups));
            $this->assertEquals($supportGroups, array_map(static function (ConditionGroup $group) {
                return clone $group->inverse();
            }, $mainGroups));
        });
    }

    /**
     * Support: `updated_at` = ? AND `created_at` = ? AND `id` <= ? OR
     *          `updated_at` = ? AND `created_at` < ? OR
     *          `updated_at` < ?
     *
     * Main:    `updated_at` = ? AND `created_at` = ? AND `id` > ? OR
     *          `updated_at` = ? AND `created_at` > ? OR
     *          `updated_at` > ?
     *
     * @param Direction $direction
     * @param Order[]   $orders
     */
    protected function assertAscendingForwardOrDescendingBackwardExclusive(Direction $direction, array $orders)
    {
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $supportGroups = ConditionGroup::createMany($this->inverseOrderArray($orders), $cursor, $direction, true, true);

        $this->specify('support query assertion', function () use ($supportGroups) {
            $where = $this->castWhereToArray($supportGroups);

            $this->assertCondition($where[0][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[0][1], 'created_at', '=', '2017-01-01 12:00:00');
            $this->assertCondition($where[0][2], 'id', '<=', 10);
            $this->assertArrayNotHasKey(3, $where[0]);

            $this->assertCondition($where[1][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[1][1], 'created_at', '<', '2017-01-01 12:00:00');
            $this->assertArrayNotHasKey(2, $where[1]);

            $this->assertCondition($where[2][0], 'updated_at', '<', '2017-01-01 18:00:00');
            $this->assertArrayNotHasKey(1, $where[2]);

            $this->assertArrayNotHasKey(3, $where);
        });

        $mainGroups = ConditionGroup::createMany($orders, $cursor, $direction, true);

        $this->specify('main query assertion', function () use ($mainGroups) {
            $where = $this->castWhereToArray($mainGroups);

            $this->assertCondition($where[0][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[0][1], 'created_at', '=', '2017-01-01 12:00:00');
            $this->assertCondition($where[0][2], 'id', '>', 10);
            $this->assertArrayNotHasKey(3, $where[0]);

            $this->assertCondition($where[1][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[1][1], 'created_at', '>', '2017-01-01 12:00:00');
            $this->assertArrayNotHasKey(2, $where[1]);

            $this->assertCondition($where[2][0], 'updated_at', '>', '2017-01-01 18:00:00');
            $this->assertArrayNotHasKey(1, $where[2]);

            $this->assertArrayNotHasKey(3, $where);
        });

        $this->specify('manually constructed inverse condition groups are equivalent to auto-generated ones', function () use ($mainGroups, $supportGroups) {
            $this->assertEquals($mainGroups, array_map(static function (ConditionGroup $group) {
                return clone $group->inverse();
            }, $supportGroups));
            $this->assertEquals($supportGroups, array_map(static function (ConditionGroup $group) {
                return clone $group->inverse();
            }, $mainGroups));
        });
    }

    /**
     * Support: `updated_at` = ? AND `created_at` = ? AND `id` > ? OR
     *          `updated_at` = ? AND `created_at` > ? OR
     *          `updated_at` > ?
     *
     * Main:    `updated_at` = ? AND `created_at` = ? AND `id` <= ? OR
     *          `updated_at` = ? AND `created_at` < ? OR
     *          `updated_at` < ?
     *
     * @param Direction $direction
     * @param Order[]   $orders
     */
    protected function assertAscendingBackwardOrDescendingForwardInclusive(Direction $direction, array $orders)
    {
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $supportGroups = ConditionGroup::createMany($this->inverseOrderArray($orders), $cursor, $direction, false, true);

        $this->specify('support query assertion', function () use ($supportGroups) {
            $where = $this->castWhereToArray($supportGroups);

            $this->assertCondition($where[0][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[0][1], 'created_at', '=', '2017-01-01 12:00:00');
            $this->assertCondition($where[0][2], 'id', '>', 10);
            $this->assertArrayNotHasKey(3, $where[0]);

            $this->assertCondition($where[1][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[1][1], 'created_at', '>', '2017-01-01 12:00:00');
            $this->assertArrayNotHasKey(2, $where[1]);

            $this->assertCondition($where[2][0], 'updated_at', '>', '2017-01-01 18:00:00');
            $this->assertArrayNotHasKey(1, $where[2]);

            $this->assertArrayNotHasKey(3, $where);
        });

        $mainGroups = ConditionGroup::createMany($orders, $cursor, $direction, false);

        $this->specify('main query assertion', function () use ($mainGroups) {
            $where = $this->castWhereToArray($mainGroups);

            $this->assertCondition($where[0][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[0][1], 'created_at', '=', '2017-01-01 12:00:00');
            $this->assertCondition($where[0][2], 'id', '<=', 10);
            $this->assertArrayNotHasKey(3, $where[0]);

            $this->assertCondition($where[1][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[1][1], 'created_at', '<', '2017-01-01 12:00:00');
            $this->assertArrayNotHasKey(2, $where[1]);

            $this->assertCondition($where[2][0], 'updated_at', '<', '2017-01-01 18:00:00');
            $this->assertArrayNotHasKey(1, $where[2]);

            $this->assertArrayNotHasKey(3, $where);
        });

        $this->specify('manually constructed inverse condition groups are equivalent to auto-generated ones', function () use ($mainGroups, $supportGroups) {
            $this->assertEquals($mainGroups, array_map(static function (ConditionGroup $group) {
                return clone $group->inverse();
            }, $supportGroups));
            $this->assertEquals($supportGroups, array_map(static function (ConditionGroup $group) {
                return clone $group->inverse();
            }, $mainGroups));
        });
    }

    /**
     * Support: `updated_at` = ? AND `created_at` = ? AND `id` >= ? OR
     *          `updated_at` = ? AND `created_at` > ? OR
     *          `updated_at` > ?
     *
     * Main:    `updated_at` = ? AND `created_at` = ? AND `id` < ? OR
     *          `updated_at` = ? AND `created_at` < ? OR
     *          `updated_at` < ?
     *
     * @param Direction $direction
     * @param Order[]   $orders
     */
    protected function assertAscendingBackwardOrDescendingForwardExclusive(Direction $direction, array $orders)
    {
        $cursor = ['id' => 10, 'created_at' => '2017-01-01 12:00:00', 'updated_at' => '2017-01-01 18:00:00'];

        $supportGroups = ConditionGroup::createMany($this->inverseOrderArray($orders), $cursor, $direction, true, true);

        $this->specify('support query assertion', function () use ($supportGroups) {
            $where = $this->castWhereToArray($supportGroups);

            $this->assertCondition($where[0][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[0][1], 'created_at', '=', '2017-01-01 12:00:00');
            $this->assertCondition($where[0][2], 'id', '>=', 10);
            $this->assertArrayNotHasKey(3, $where[0]);

            $this->assertCondition($where[1][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[1][1], 'created_at', '>', '2017-01-01 12:00:00');
            $this->assertArrayNotHasKey(2, $where[1]);

            $this->assertCondition($where[2][0], 'updated_at', '>', '2017-01-01 18:00:00');
            $this->assertArrayNotHasKey(1, $where[2]);

            $this->assertArrayNotHasKey(3, $where);
        });

        $mainGroups = ConditionGroup::createMany($orders, $cursor, $direction, true);

        $this->specify('main query assertion', function () use ($mainGroups) {
            $where = $this->castWhereToArray($mainGroups);

            $this->assertCondition($where[0][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[0][1], 'created_at', '=', '2017-01-01 12:00:00');
            $this->assertCondition($where[0][2], 'id', '<', 10);
            $this->assertArrayNotHasKey(3, $where[0]);

            $this->assertCondition($where[1][0], 'updated_at', '=', '2017-01-01 18:00:00');
            $this->assertCondition($where[1][1], 'created_at', '<', '2017-01-01 12:00:00');
            $this->assertArrayNotHasKey(2, $where[1]);

            $this->assertCondition($where[2][0], 'updated_at', '<', '2017-01-01 18:00:00');
            $this->assertArrayNotHasKey(1, $where[2]);

            $this->assertArrayNotHasKey(3, $where);
        });

        $this->specify('manually constructed inverse condition groups are equivalent to auto-generated ones', function () use ($mainGroups, $supportGroups) {
            $this->assertEquals($mainGroups, array_map(static function (ConditionGroup $group) {
                return clone $group->inverse();
            }, $supportGroups));
            $this->assertEquals($supportGroups, array_map(static function (ConditionGroup $group) {
                return clone $group->inverse();
            }, $mainGroups));
        });
    }

    #[Test]
    public function testInvalidComparatorForNonPrimaryKey(): void
    {
        $this->expectException(\Lampager\Exceptions\Query\BadKeywordException::class);
        $this->expectExceptionMessage('Comparator for non-primary key condition must be "<", ">" or "="');

        new Condition('created_at', '<=', '2017-01-01 12:00:00');
    }

    #[Test]
    public function testInvalidComparatorForPrimaryKey(): void
    {
        $this->expectException(\Lampager\Exceptions\Query\BadKeywordException::class);
        $this->expectExceptionMessage('Comparator for primary key condition must be "<", ">", "<=" or ">="');

        new Condition('id', '=', 10, true);
    }

    #[Test]
    public function testMissingCursorParameter(): void
    {
        try {
            $orders = Order::createMany([['updated_at', Order::ASC], ['created_at', Order::ASC], ['id', Order::ASC]]);
            $direction = new Direction(Direction::FORWARD);
            $cursor = ['id' => 10, 'updated_at' => '2017-01-01 18:00:00'];
            ConditionGroup::create($orders, $cursor, $direction, false, true, false);
        } catch (CursorParameterException $e) {
            $this->assertSame('Missing cursor parameter: created_at', $e->getMessage());
            $this->assertSame('created_at', $e->getColumn());
        }
    }

    #[Test]
    public function testDeepClone(): void
    {
        $condition = new Condition('id', '>=', 10, true);
        $group = new ConditionGroup([$condition]);

        $cloneGroup = clone $group;
        $cloneCondition = $cloneGroup->conditions()[0];

        $this->assertEquals($condition, $cloneCondition);
        $this->assertNotSame($condition, $cloneCondition);
        $this->assertEquals($group, $cloneGroup);
        $this->assertNotSame($condition, $cloneCondition);
    }


    /**
     * Polyfill for removed Codeception method
     *
     * @param string $name
     * @param callable $fn
     * @return void
     */
    private function specify($name, $fn): void
    {
        try {
            $fn();
        } catch (\Exception $e) {
        } catch (\Throwable $e) {
        }

        if (isset($e)) {
            echo "Specified assertion failed: $name\n";
            throw $e;
        }
    }
}
