<?php

namespace Lampager\Query;

/**
 * Class Condition
 */
class Condition
{
    const LT = '<';
    const GT = '>';
    const EQ = '=';
    const LE = '<=';
    const GE = '>=';

    protected static $comparatorInverseMap = [
        // inverse map for non-primary key condition
        [
            self::LT => self::GT,
            self::EQ => self::EQ,
            self::GT => self::LT,
        ],
        // inverse map for primary key condition
        [
            self::LT => self::GE,
            self::GT => self::LE,
            self::LE => self::GT,
            self::GE => self::LT,
        ],
    ];

    protected static $comparatorOrderDirectionMap = [
        Order::ASCENDING => [
            Direction::FORWARD => self::GT,
            Direction::BACKWARD => self::LT,
        ],
        Order::DESCENDING => [
            Direction::FORWARD => self::LT,
            Direction::BACKWARD => self::GT,
        ],
    ];

    /**
     * @var string
     */
    protected $left;

    /**
     * @var string
     */
    protected $comparator;

    /**
     * @var string
     */
    protected $right;

    /**
     * @var bool
     */
    protected $isPrimaryKey;

    /**
     * @param  Order      $order
     * @param  int|string $value
     * @param  Direction  $direction
     * @param  bool       $exclusive
     * @param  bool       $isPrimaryKey
     * @param  bool       $isLastKey
     * @param  bool       $isSupportQuery
     * @return Condition
     */
    public static function create(Order $order, $value, Direction $direction, $exclusive, $isPrimaryKey, $isLastKey, $isSupportQuery = false)
    {
        return new self(
            $order->column(),
            static::compileComparator(
                $order,
                $direction,
                $exclusive,
                $isPrimaryKey,
                $isLastKey,
                $isSupportQuery
            ),
            $value,
            $isPrimaryKey
        );
    }

    /**
     * @param  Order     $order
     * @param  Direction $direction
     * @param  bool      $exclusive
     * @param  bool      $isPrimaryKey
     * @param  bool      $isLastKey
     * @param  bool      $isSupportQuery
     * @return string
     */
    protected static function compileComparator(Order $order, Direction $direction, $exclusive, $isPrimaryKey, $isLastKey, $isSupportQuery)
    {
        if (!$isLastKey) {
            // Comparator for keys except the last one is always "=".
            // e.g. updated_at = ? AND created_at = ? AND id > ?
            return static::EQ;
        }

        // e.g. Ascending forward uses the condition "column > ?"
        $base = static::$comparatorOrderDirectionMap[$order->order()][(string)$direction];

        // For main query, we append "=" to the primary key condition when it is inclusive.
        $shouldAppendEquals = $isPrimaryKey && (!$isSupportQuery && !$exclusive || $isSupportQuery && $exclusive);

        return $base . ($shouldAppendEquals ? self::EQ : '');
    }

    /**
     * Condition constructor.
     *
     * @param string     $left
     * @param string     $comparator
     * @param int|string $right
     * @param bool       $isPrimaryKey
     */
    public function __construct($left, $comparator, $right, $isPrimaryKey = false)
    {
        $this->left = (string)$left;
        $this->comparator = (string)static::validate($comparator, $isPrimaryKey);
        $this->right = $right;
        $this->isPrimaryKey = (bool)$isPrimaryKey;
    }

    /**
     * @param  string $comparator
     * @param  bool   $isPrimaryKey
     * @return string
     */
    protected static function validate($comparator, $isPrimaryKey)
    {
        if (!isset(static::$comparatorInverseMap[$isPrimaryKey][$comparator])) {
            throw new \DomainException(
                $isPrimaryKey
                    ? 'Comparator for primary key condition must be "<", ">", "<=" or ">="'
                    : 'Comparator for non-primary key condition must be "<", ">" or "="'
            );
        }
        return $comparator;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [$this->left, $this->comparator, $this->right];
    }

    /**
     * @return string
     */
    public function left()
    {
        return $this->left;
    }

    /**
     * @return string
     */
    public function comparator()
    {
        return $this->comparator;
    }

    /**
     * @return string
     */
    public function right()
    {
        return $this->right;
    }

    /**
     * @return bool
     */
    public function isPrimaryKey()
    {
        return $this->isPrimaryKey;
    }

    /**
     * @return static
     */
    public function inverse()
    {
        return new static(
            $this->left,
            static::$comparatorInverseMap[$this->isPrimaryKey][$this->comparator],
            $this->right,
            $this->isPrimaryKey
        );
    }
}
