<?php

namespace Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig;

enum TaxonomyFilterType: string
{
    case MULTISELECT  = 'multi';
    case SINGLESELECT = 'single';
}
