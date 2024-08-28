<?php

namespace Municipio\Config\Features\ExternalContent;

use Municipio\Config\Contracts\FeatureIsEnabled;
use Municipio\Config\Contracts\GetEnabledPostTypes;
use Municipio\Config\Features\ExternalContent\Contracts\GetPostTypeSettings;

interface ExternalContentConfigInterface extends
    FeatureIsEnabled,
    GetEnabledPostTypes,
    GetPostTypeSettings
{
}
