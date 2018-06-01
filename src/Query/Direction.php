<?php

namespace Lampager\Query;

use Lampager\Exceptions\Query\BadKeywordException;

/**
 * Class Direction
 */
class Direction
{
    const FORWARD = 'forward';
    const BACKWARD = 'backward';

    /**
     * @var string
     */
    protected $direction;

    /**
     * Direction constructor.
     *
     * @param string $direction
     */
    public function __construct($direction)
    {
        $this->direction = static::validate($direction);
    }

    /**
     * @param $direction
     * @return string
     */
    protected static function validate($direction)
    {
        $direction = strtolower($direction);
        if (!preg_match('/\A(forward|backward)\z/', $direction, $m)) {
            throw new BadKeywordException('Direction must be "forward" or "backward"');
        }
        return $m[1];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->direction;
    }

    /**
     * @return bool
     */
    public function forward()
    {
        return $this->direction === static::FORWARD;
    }

    /**
     * @return bool
     */
    public function backward()
    {
        return $this->direction === static::BACKWARD;
    }

    /**
     * @return static
     */
    public function inverse()
    {
        return new static($this->direction === static::FORWARD ? static::BACKWARD : static::FORWARD);
    }
}
