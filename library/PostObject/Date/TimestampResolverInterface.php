<?php

namespace Municipio\PostObject\Date;

interface TimestampResolverInterface
{
    public function resolve(): int;
}
