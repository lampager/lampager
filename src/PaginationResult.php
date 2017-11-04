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
    public $has_previous;

    /**
     * @var null|mixed
     */
    public $previous_cursor;

    /**
     * @var null|bool
     */
    public $has_next;

    /**
     * @var null|mixed
     */
    public $next_cursor;

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
