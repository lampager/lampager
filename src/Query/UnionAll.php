<?php

namespace Lampager\Query;

/**
 * Class UnionAll
 */
class UnionAll extends SelectOrUnionAll implements \IteratorAggregate
{
    /**
     * @var Select
     */
    protected $mainQuery;

    /**
     * @var Select
     */
    protected $supportQuery;

    /**
     * UnionAll constructor.
     *
     * @param Select $select
     */
    public function __construct(Select $select)
    {
        $this->mainQuery = $select;
        $this->supportQuery = $select->inverse();
    }

    /**
     * @return Select
     */
    public function mainQuery()
    {
        return $this->mainQuery;
    }

    /**
     * @return Select
     */
    public function supportQuery()
    {
        return $this->supportQuery;
    }

    /**
     * Clone Select.
     */
    public function __clone()
    {
        $this->mainQuery = clone $this->mainQuery;
        $this->supportQuery = clone $this->supportQuery;
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Generator|Select[]
     */
    public function getIterator()
    {
        yield $this->mainQuery;
        yield $this->supportQuery;
    }
}
