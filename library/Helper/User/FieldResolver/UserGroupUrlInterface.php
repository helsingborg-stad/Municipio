<?php

namespace Municipio\Helper\User\FieldResolver;

interface UserGroupUrlInterface
{
    /**
     * Get the resolved URL.
     *
     * @return string|null
     */
    public function get(): ?string;
}