<?php

namespace Lampager\Exceptions\Query;

use Lampager\Contracts\Exceptions\Query\BadQueryException;
use Lampager\Exceptions\OutOfBoundsException;

/**
 * Class MissingCursorParameterException
 */
class MissingCursorParameterException extends OutOfBoundsException implements BadQueryException
{
}
