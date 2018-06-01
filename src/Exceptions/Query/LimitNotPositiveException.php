<?php

namespace Lampager\Exceptions\Query;

use Lampager\Contracts\Exceptions\Query\BadQueryException;
use Lampager\Exceptions\OutOfRangeException;

/**
 * Class LimitNotPositiveException
 */
class LimitNotPositiveException extends OutOfRangeException implements BadQueryException
{
}
