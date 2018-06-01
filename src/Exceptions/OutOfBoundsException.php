<?php

namespace Lampager\Exceptions;

use Lampager\Contracts\Exceptions\LampagerException;

/**
 * Class OutOfBoundsException
 */
class OutOfBoundsException extends \OutOfBoundsException implements LampagerException
{
}
