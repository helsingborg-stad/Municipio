<?php

namespace Municipio\Styleguide\Customize\OverrideState;

use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetThemeMod;

interface OverrideStateInterface
{
    /**
     * Get the current override state from the database, and apply filters to it before returning. The override state is stored as a JSON string in the theme mods, and should be decoded into an array before being returned.
     * The returned array should have the following structure:
     * @return array{
     *     token: array,
     *     component: array,
     * }
     */
    public function getOverrideState(): array;
}
