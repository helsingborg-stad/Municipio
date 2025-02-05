<?php

namespace Municipio\PostObject\Date;

interface TimestampResolverInterface
{
    /**
     * Resolve the timestamp.
     *
     * @return int
     */
    public function resolve(): int;
}
