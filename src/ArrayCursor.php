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
    public function has($column)
    {
        return isset($this->cursor[$column]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($column)
    {
        return isset($this->cursor[$column]) ? $this->cursor[$column] : null;
    }
}
