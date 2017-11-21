<?php

namespace Lampager;

/**
 * Class PaginationResult
 */
class PaginationResult implements \IteratorAggregate, \Countable
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
     * @return \Traversable|\ArrayIterator
     */
    public function getIterator()
    {
        return $this->records instanceof \Traversable ? $this->records : new \ArrayIterator($this->records);
    }

    /**
     * Count records.
     *
     * @return int
     */
    public function count()
    {
        return $this->records instanceof \Countable || is_array($this->records) ? count($this->records) : 0;
    }
}
