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
        $default = json_encode(['token' => [], 'component' => []]);
        $stored = json_decode($this->wpService->getThemeMod('tokens', $default), true);

        $stored = $this->wpService->applyFilters('Municipio/Styleguide/Customize/OverrideState', $stored);

        if (!is_array($stored)) {
            return ['token' => [], 'component' => []];
        }

        $state = [
            'token' => is_array($stored['token'] ?? null) ? $stored['token'] : [],
            'component' => is_array($stored['component'] ?? null) ? $stored['component'] : [],
        ];

        foreach (['_dark', '_contrast'] as $scheme) {
            if (array_key_exists($scheme, $stored) && (is_array($stored[$scheme]) || $stored[$scheme] === null)) {
                $state[$scheme] = $stored[$scheme];
            }
        }

        return $state;
    }
}
