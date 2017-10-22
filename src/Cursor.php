<?php

namespace Lampager;

/**
 * Interface Cursor
 */
interface Cursor
{
    /**
     * Return a value indicating whether the cursor has the column.
     *
     * @param  string $column Column.
     * @return bool
     */
    public function has($column);

    /**
     * Return a cursor specified by the column.
     *
     * @param  string $column Column.
     * @return int|string
     */
    public function get($column);
}
