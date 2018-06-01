<?php

namespace Lampager\Exceptions\Query;

use Lampager\Contracts\Exceptions\Query\BadQueryException;
use Lampager\Exceptions\DomainException;

/**
 * Class BadKeywordException
 */
class BadKeywordException extends DomainException implements BadQueryException
{
}
