<?php

namespace Lampager\Exceptions;

use Lampager\Contracts\Exceptions\LampagerException;

/**
 * Class DomainException
 */
class DomainException extends \DomainException implements LampagerException
{
}
