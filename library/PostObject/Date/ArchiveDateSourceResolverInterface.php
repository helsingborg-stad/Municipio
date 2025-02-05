<?php

namespace Municipio\PostObject\Date;

/**
 * ArchiveDateSourceResolverInterface interface.
 */
interface ArchiveDateSourceResolverInterface
{
    /**
     * Resolve the archive date setting.
     */
    public function resolve(): string;
}
