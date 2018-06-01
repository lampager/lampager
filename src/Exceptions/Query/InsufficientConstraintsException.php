<?php

namespace Lampager\Exceptions\Query;

use Lampager\Contracts\Exceptions\Query\BadQueryException;
use Lampager\Exceptions\OutOfRangeException;

/**
 * Class InsufficientConstraintsException
 */
class InsufficientConstraintsException extends OutOfRangeException implements BadQueryException
{
}
