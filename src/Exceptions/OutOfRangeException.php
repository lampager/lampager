<?php

namespace Lampager\Exceptions;

use Lampager\Contracts\Exceptions\LampagerException;

/**
 * Class OutOfRangeException
 */
class OutOfRangeException extends \OutOfRangeException implements LampagerException
{
}
