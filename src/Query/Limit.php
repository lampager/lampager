<?php

namespace Lampager\Query;

use Lampager\Exceptions\Query\LimitParameterException;

/**
 * Class Limit
 */
class Limit
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $originalLimit;

    /**
     * @var bool
     */
    protected $isSupportQuery;

    /**
     * Limit constructor.
     *
     * @param int  $originalLimit
     * @param bool $isSupportQuery
     */
    public function __construct($originalLimit, $isSupportQuery = false)
    {
        $this->limit = static::validate($originalLimit, $isSupportQuery);
        $this->originalLimit = (int)$originalLimit;
        $this->isSupportQuery = (bool)$isSupportQuery;
    }

    /**
     * @param  int  $limit
     * @param  bool $isSupportQuery
     * @return int
     */
    protected static function validate($limit, $isSupportQuery)
    {
        if (!ctype_digit("$limit")) {
            throw new LimitParameterException('Limit must be integer');
        }
        if ($limit < 1) {
            throw new LimitParameterException('Limit must be positive integer');
        }
        return $isSupportQuery ? 1 : ($limit + 1);
    }

    /**
     * @return int
     */
    public function toInteger()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function original()
    {
        return $this->originalLimit;
    }

    /**
     * @return bool
     */
    public function isMainQuery()
    {
        return !$this->isSupportQuery;
    }

    /**
     * @return bool
     */
    public function isSupportQuery()
    {
        return $this->isSupportQuery;
    }

    /**
     * @return static
     */
    public function inverse()
    {
        return new static($this->originalLimit, !$this->isSupportQuery);
    }
}
