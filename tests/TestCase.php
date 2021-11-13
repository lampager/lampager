<?php

namespace Lampager\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @param int $number
     * @return int|string
     */
    protected function number($number)
    {
        return version_compare(PHP_VERSION, '8.1', '>=') ? $number : "$number";
    }
}
