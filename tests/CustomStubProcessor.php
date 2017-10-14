<?php

namespace Lampager\Tests;

use Lampager\ArrayProcessor;
use Lampager\Query\Query;

class CustomStubProcessor extends ArrayProcessor
{
    /**
     * @param  Query $query
     * @param  mixed $rows
     * @return array
     */
    public function process(Query $query, $rows)
    {
        return ['This is dummy'];
    }
}
