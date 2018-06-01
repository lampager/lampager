<?php

namespace Lampager\Exceptions;

use Lampager\Contracts\Exceptions\LampagerException;

/**
 * Class UnexpectedValueException
 */
class UnexpectedValueException extends \UnexpectedValueException implements LampagerException
{
}
