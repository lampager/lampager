<?php

namespace Lampager\Exceptions\Query;

use Lampager\Contracts\Exceptions\Query\BadQueryException;
use Lampager\Exceptions\UnexpectedValueException;

/**
 * Class CursorParameterException
 */
class CursorParameterException extends UnexpectedValueException implements BadQueryException
{
}
