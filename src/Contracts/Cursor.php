<?php

namespace Lampager\Contracts;

/**
 * Interface Cursor
 */
interface Cursor
{
    /**
     * Return a value indicating whether the cursor is non-empty and has the specified columns.
     *
     * @return null|bool null if the cursor is empty; true if the cursor has all the columns; otherwise false.
     */
    public function has(...$columns);

    /**
     * Return a cursor specified by the column.
     *
     * @param  string          $column Column.
     * @return null|int|string
     */
    public function get($column);
}
