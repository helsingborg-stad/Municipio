<?php

namespace Municipio\Config\Contracts;

interface GetEnabledPostTypes
{
    /**
     * Get post types with schema types enabled.
     *
     * @return array Post types.
     */
    public function getEnabledPostTypes(): array;
}
