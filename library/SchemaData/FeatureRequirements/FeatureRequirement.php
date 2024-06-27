<?php

namespace Municipio\SchemaData\FeatureRequirements;

interface FeatureRequirement
{
    /**
     * Check if the feature requirement is met.
     *
     * @return bool True if the requirement is met, false otherwise.
     */
    public function isMet(): bool;
}
