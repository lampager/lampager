<?php

namespace Lampager;

/**
 * Class PaginationResult
 */
class PaginationResult implements \IteratorAggregate
{
    /**
     * @var mixed
     */
    public $records;

    /**
     * @var null|bool
     */
    public $hasPrevious;

    /**
     * @var null|mixed
     */
    public $previousCursor;

    /**
     * @var null|bool
     */
    public $hasNext;

    /**
     * @var null|mixed
     */
    public $nextCursor;

    /**
     * PaginationResult constructor.
     * Merge $meta entries into $this.
     *
     * @param mixed $rows
     * @param array $meta
     */
    public function __construct($rows, array $meta)
    {
        $this->records = $rows;
        foreach ($meta as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get iterator of records.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->records);
    }
}
