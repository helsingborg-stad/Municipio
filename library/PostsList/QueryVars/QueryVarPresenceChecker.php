<?php

declare(strict_types=1);

namespace Municipio\PostsList\QueryVars;

use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfigInterface;
use Municipio\PostsList\QueryVars\QueryVarsInterface;

class QueryVarPresenceChecker
{
    /**
     * Checks if any relevant query var is present in $_GET.
     *
     * @param QueryVarsInterface $queryVars
     * @param TaxonomyFilterConfigInterface[] $taxonomyFilterConfigs
     * @return bool
     */
    public static function isAnyQueryVarPresent(QueryVarsInterface $queryVars, array $taxonomyFilterConfigs = []): bool
    {
        if (!empty($_GET[$queryVars->getSearchParameterName()]) || !empty($_GET[$queryVars->getDateFromParameterName()]) || !empty($_GET[$queryVars->getDateToParameterName()])) {
            return true;
        }

        foreach ($taxonomyFilterConfigs as $config) {
            if (!empty($_GET[$queryVars->getPrefix() . $config->getTaxonomy()->name])) {
                return true;
            }
        }

        return false;
    }
}
