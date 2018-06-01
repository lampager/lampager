<?php

namespace Lampager\Exceptions;

use Lampager\Contracts\Exceptions\LampagerException;

/**
 * Class InvalidArgumentException
 */
class InvalidArgumentException extends \InvalidArgumentException implements LampagerException
{
}
