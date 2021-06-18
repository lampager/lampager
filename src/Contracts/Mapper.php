<?php

namespace Lampager\Contracts;

/**
 * Interface Mapper
 */
interface Mapper
{
    /**
     * Determines which pre-SQL column/cursor corresponds to which post-SQL field.
     *
     * @param string $columnOrCursorName
     * @param string $retrievedFieldName
     * @return string
     */
    public function map($columnOrCursorName, $retrievedFieldName);
}
