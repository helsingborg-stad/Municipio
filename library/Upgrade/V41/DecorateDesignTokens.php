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

        if (!isset($tokens['button_default_color_active']) || empty($tokens['button_default_color_active'])) {
            unset($tokens['component']['__general__']['button']['--c-button--color--surface-alt']);
            unset($tokens['component']['__general__']['button']['--c-button--color--surface-contrast']);
        }

        if (!isset($tokens['button_primary_color_active']) || empty($tokens['button_primary_color_active'])) {
            unset($tokens['component']['__general__']['button']['--c-button--color--primary']);
            unset($tokens['component']['__general__']['button']['--c-button--color--primary-contrast']);
        }

        if (!isset($tokens['button_secondary_color_active']) || empty($tokens['button_secondary_color_active'])) {
            unset($tokens['component']['__general__']['button']['--c-button--color--secondary']);
            unset($tokens['component']['__general__']['button']['--c-button--color--secondary-contrast']);
        }

        if (isset($tokens['component']['__general__']['brand']['--c-brand--font-size-multiplier']) && !empty($tokens['component']['__general__']['brand']['--c-brand--font-size-multiplier'])) {
            $multiplier = (float) $tokens['component']['__general__']['brand']['--c-brand--font-size-multiplier'] / 2.1;
            $tokens['component']['__general__']['brand']['--c-brand--font-size-multiplier'] = $multiplier;
        }

        return $tokens;
    }
}
