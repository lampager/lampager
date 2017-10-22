<?php

namespace Lampager\Contracts;

use Lampager\Query;

/**
 * Interface Formatter
 */
interface Formatter
{
    /**
     * Format rows.
     *
     * @param  mixed $rows
     * @param  array $meta
     * @param  Query $query
     * @return mixed
     */
    public function format($rows, array $meta, Query $query);
}
