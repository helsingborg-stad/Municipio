<?php

namespace Municipio\PostObject\Date;

interface ArchiveDateFormatResolverInterface
{
    /**
     * Resolve the archive date format.
     */
    public function resolve(): string;
}