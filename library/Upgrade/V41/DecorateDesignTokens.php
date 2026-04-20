<?php

namespace Municipio\Upgrade\V41;

class DecorateDesignTokens
{
    public function decorate(array $tokens): array
    {
        if (!is_null($tokens['token']['--container-width'] ?? null)) {
            $tokens['token']['--container-width'] .= 'px';
        }

        if (!is_null($tokens['token']['--border-radius'] ?? null)) {
            $tokens['token']['--border-radius'] = (float) $tokens['token']['--border-radius'] / 8;
        }

        if (!is_null($tokens['token']['--border-width'] ?? null)) {
            $tokens['token']['--border-width'] = (float) $tokens['token']['--border-width'] / 8;
        }

        if (!is_null($tokens['component']['__general__']['field']['--c-field--border-radius'] ?? null)) {
            $tokens['component']['__general__']['field']['--c-field--border-radius'] = (string) (float) $tokens['component']['__general__']['field']['--c-field--border-radius'] / 4;
        }

        if (!is_null($tokens['component']['__general__']['card']['--c-card--border-width'] ?? null)) {
            $tokens['component']['__general__']['card']['--c-card--border-width'] = (string) (float) $tokens['component']['__general__']['card']['--c-card--border-width'] / 8;
        }

        if (!is_null($tokens['component']['__general__']['header']['--c-header--logotype-height-multiplier'] ?? null)) {
            // Legacy setting was an absolute multiplier for base units (default 6).
            $tokens['component']['__general__']['header']['--c-header--logotype-height-multiplier'] = (float) $tokens['component']['__general__']['header']['--c-header--logotype-height-multiplier'] / 6;
        }

        if (!is_null($tokens['component']['__general__']['footer']['--c-footer--home-link-height-multiplier'] ?? null)) {
            // Legacy setting used a 3-12 scale with default 6.
            $tokens['component']['__general__']['footer']['--c-footer--home-link-height-multiplier'] = (float) $tokens['component']['__general__']['footer']['--c-footer--home-link-height-multiplier'] / 6;
        }

        if (!is_null($tokens['token']['--space'] ?? null)) {
            // convert from 0-12 scale to 0-2 scale
            $tokens['token']['--space'] = ((float) $tokens['token']['--space'] / 12) * 2;
        }

        if (!is_null($tokens['token']['--outer-space'] ?? null)) {
            // convert from 0-12 scale to 0-3 scale
            $tokens['token']['--outer-space'] = ((float) $tokens['token']['--outer-space'] / 12) * 3;
        }

        return $tokens;
    }
}
