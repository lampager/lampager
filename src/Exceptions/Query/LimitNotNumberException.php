<?php

namespace Lampager\Exceptions\Query;

use Lampager\Contracts\Exceptions\Query\BadQueryException;
use Lampager\Exceptions\DomainException;

/**
 * Class LimitNotNumberException
 */
class LimitNotNumberException extends DomainException implements BadQueryException
{
}
