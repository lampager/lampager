<?php

namespace Lampager\Exceptions\Query;

use Lampager\Contracts\Exceptions\Query\BadQueryException;
use Lampager\Exceptions\DomainException;

/**
 * Class LimitParameterException
 */
class LimitParameterException extends DomainException implements BadQueryException
{
}
