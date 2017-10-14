<?php

namespace Lampager\Tests;

use Lampager\ArrayProcessor;
use Lampager\Concerns\HasProcessor;
use Lampager\Paginator;
use Lampager\Query\Select;
use Lampager\Query\SelectOrUnionAll;
use Lampager\Query\UnionAll;

class StubPaginator extends Paginator
{
    use HasProcessor;

    public static $rows = [
        ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
        ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
        ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
        ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
        ['id' => 4, 'updated_at' => '2017-01-01 11:00:00'],
    ];

    /**
     * @var \PDO
     */
    protected static $pdo;

    /**
     * @var array
     */
    protected $binds = [];

    public static function boot()
    {
        if (static::$pdo) {
            return;
        }
        static::$pdo = new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
        static::$pdo->exec('CREATE TABLE posts(id INTEGER PRIMARY KEY, updated_at TEXT)');
        $stmt = static::$pdo->prepare('INSERT INTO posts(id, updated_at) VALUES (?, ?)');
        foreach (static::$rows as $row) {
            $stmt->bindValue(1, $row['id'], \PDO::PARAM_INT);
            $stmt->bindValue(2, $row['updated_at'], \PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    /**
     * StubPaginator constructor.
     *
     * @param $table
     */
    public function __construct($table)
    {
        static::boot();
        $this->builder = $table;
        $this->processor = new ArrayProcessor();
    }

    /**
     * @param  Select $select
     * @return string
     */
    protected function compileWhere(Select $select)
    {
        $ors = [];
        foreach ($select->where() ?: [] as $group) {
            $ands = [];
            foreach ($group as $condition) {
                $ands[] = "{$condition->left()} {$condition->comparator()} ?";
                $this->binds[] = $condition->right();
            }
            $ors[] = implode(' AND ', $ands);
        }
        return implode(' OR ', $ors);
    }

    /**
     * @param  Select $select
     * @return array
     */
    protected function compileOrders(Select $select)
    {
        $orders = [];
        foreach ($select->orders() as $order) {
            $orders[] = "{$order->column()} {$order->order()}";
        }
        return implode(', ', $orders);
    }

    /**
     * @param  Select $select
     * @return string
     */
    protected function compileLimit(Select $select)
    {
        return (string)$select->limit()->toInteger();
    }

    /**
     * @param  Select $select
     * @return string
     */
    protected function compileSelect(Select $select)
    {
        $where = $this->compileWhere($select);
        $WHERE = $where ? "WHERE {$where}" : '';
        return "
            SELECT * FROM {$this->builder}
            $WHERE
            ORDER BY {$this->compileOrders($select)}
            LIMIT {$this->compileLimit($select)}
        ";
    }

    /**
     * @param  SelectOrUnionAll $selectOrUnionAll
     * @return string
     */
    protected function compileSelectOrUnionAll(SelectOrUnionAll $selectOrUnionAll)
    {
        if ($selectOrUnionAll instanceof Select) {
            return $this->compileSelect($selectOrUnionAll);
        }
        if ($selectOrUnionAll instanceof UnionAll) {
            return "
                SELECT * FROM ({$this->compileSelect($selectOrUnionAll->supportQuery())})
                UNION ALL
                SELECT * FROM ({$this->compileSelect($selectOrUnionAll->mainQuery())})
            ";
        }
        throw new \LogicException('Unreachable here');
    }

    /**
     * @param  array $cursor
     * @return mixed
     */
    public function paginate(array $cursor = [])
    {
        $query = $this->configure($cursor);
        $sql = $this->compileSelectOrUnionAll($query->selectOrUnionAll());
        $stmt = static::$pdo->prepare($sql);
        $stmt->execute($this->binds);
        return $this->process($query, $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }
}
