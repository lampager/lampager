<?php

namespace Lampager;

class ArrayProcessor extends AbstractProcessor
{
    /**
     * Return comparable raw value from a row.
     *
     * @param  mixed      $row
     * @param  string     $column
     * @return int|string
     */
    protected function rawField($row, $column)
    {
        return is_object($row) && !$row instanceof \ArrayAccess ? $row->$column : $row[$column];
    }

    /**
     * Return the n-th element of collection.
     * Must return null if not exists.
     *
     * @param  array $rows
     * @param  int   $offset
     * @return mixed
     */
    protected function offset($rows, $offset)
    {
        return isset($rows[$offset]) ? $rows[$offset] : null;
    }

    /**
     * Slice rows, like PHP function array_slice().
     *
     * @param  array    $rows
     * @param  int      $offset
     * @param  null|int $length
     * @return array
     */
    protected function slice($rows, $offset, $length = null)
    {
        return array_slice($rows, $offset, $length);
    }

    /**
     * Count rows, like PHP function count().
     *
     * @param  array $rows
     * @return int
     */
    protected function count($rows)
    {
        return count($rows);
    }

    /**
     * Reverse rows, like PHP function array_reverse().
     *
     * @param  array $rows
     * @return array
     */
    protected function reverse($rows)
    {
        return array_reverse($rows);
    }
}
