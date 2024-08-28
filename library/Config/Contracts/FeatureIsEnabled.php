<?php

namespace Municipio\Config\Contracts;

interface FeatureIsEnabled
{
    /**
     * Check if feature is enabled.
     *
     * @return bool
     */
    public function featureIsEnabled(): bool;
}
