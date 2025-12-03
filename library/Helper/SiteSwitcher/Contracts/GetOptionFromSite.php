<?php

namespace Municipio\Helper\SiteSwitcher\Contracts;

interface GetOptionFromSite
{
    /**
     * Get an option from a specific site.
     *
     * @param int $siteId
     * @param string $optionName
     *
     * @return mixed
     */
    public function getOptionFromSite(int $siteId, string $optionName): mixed;
}
