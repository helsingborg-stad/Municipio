<?php

namespace Municipio\PostObject\Date;

/**
 * ArchiveDateSettingResolverInterface interface.
 */
interface ArchiveDateSettingResolverInterface
{
    /**
     * Resolve the archive date setting.
     */
    public function resolve(): string;
}
