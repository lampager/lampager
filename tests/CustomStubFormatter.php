<?php

namespace Lampager\Tests;

use Lampager\Contracts\Formatter;
use Lampager\PaginationResult;
use Lampager\Query;

class CustomStubFormatter implements Formatter
{
    public function format($rows, array $meta, Query $query)
    {
        $meta['called_method'] = __METHOD__;
        return new PaginationResult($rows, $meta);
    }

    public function __invoke($rows, array $meta, Query $query)
    {
        $meta['called_method'] = __METHOD__;
        return new PaginationResult($rows, $meta);
    }
}
