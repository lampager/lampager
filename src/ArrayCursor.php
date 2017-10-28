<?php

namespace Lampager;

use Lampager\Contracts\Cursor;

/**
 * Default implementation for Cursor
 */
class ArrayCursor implements Cursor
{
    /** @var int[]|string[] */
    protected $cursor;

    /**
     * @param int[]|string[] $cursor
     */
    public function __construct(array $cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * {@inheritdoc}
     */
    public function has(...$columns)
    {
        if (empty($this->cursor)) {
            return null;
        }
        foreach ($columns as $column) {
            if (!isset($this->cursor[$column])) {
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($column)
    {
        return isset($this->cursor[$column]) ? $this->cursor[$column] : null;
    }
}
