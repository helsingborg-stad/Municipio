<?php

namespace Municipio\Helper\SiteSwitcher\Contracts;

interface GetFieldFromSite
{
    /**
     * Get an acf field from a specific site.
     * 
     * @param int $siteId
     * @param string $fieldSelector The field name or key. 
     * 
     * @return mixed
     */
    public function getFieldFromSite(int $siteId, string $fieldSelector): mixed;
}
