<?php

namespace Lampager;

use Lampager\Contracts\Mapper;

class ArrayMapper implements Mapper
{
    /**
     * @var string[]
     */
    protected $mapping;

    /**
     * ArrayMapper constructor.
     *
     * @param string[] $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Determines which pre-SQL column/cursor corresponds to which post-SQL field.
     *
     * @param string $columnOrCursorName
     * @param string $retrievedFieldName
     * @return string
     */
    public function map($columnOrCursorName, $retrievedFieldName)
    {
        return isset($this->mapping[$columnOrCursorName])
            ? $this->mapping[$columnOrCursorName]
            : $retrievedFieldName;
    }
}
