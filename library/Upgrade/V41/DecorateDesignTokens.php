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

        if (!is_null($tokens['component']['__general__']['header']['--c-header--logotype-height'] ?? null)) {
            $tokens['component']['__general__']['header']['--c-header--logotype-height'] = ((int) $tokens['component']['__general__']['header']['--c-header--logotype-height'] * 8) . 'px';
        }

        return $tokens;
    }
}
