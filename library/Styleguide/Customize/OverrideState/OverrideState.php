<?php

namespace Municipio\Styleguide\Customize\OverrideState;

use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetThemeMod;

class OverrideState implements OverrideStateInterface
{
    public function __construct(
        private GetThemeMod&ApplyFilters $wpService,
    ) {}

    public function getOverrideState(): array
    {
        $default = json_encode(['design' => ['token' => [], 'component' => []]]);
        $stored = json_decode($this->wpService->getThemeMod('tokens', $default), true);

        $stored = $this->wpService->applyFilters('Municipio/Styleguide/Customize/OverrideState', $stored);

        return [
            'token' => $stored['token'],
            'component' => $stored['component'],
        ];
    }
}
