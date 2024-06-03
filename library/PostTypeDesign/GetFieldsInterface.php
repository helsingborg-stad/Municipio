<?php

namespace Municipio\PostTypeDesign;

/**
 * Interface GetFieldsInterface
 *
 * This interface defines the methods for retrieving fields and field keys.
 */
interface GetFieldsInterface
{
    /**
     * Get the fields.
     *
     * @return array An array of fields.
     */
    public function getFields(): array;

    /**
     * Get the field keys.
     *
     * @return array An array of field keys.
     */
    public function getFieldKeys(): array;
}
