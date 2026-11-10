<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ApplyStyles\DesignTokensToCssConverter;

use Municipio\Styleguide\Customize\ApplyStyles\ColorSchemeOverrides;

/** Serializes validated custom properties in root, component and named scopes. */
class DesignTokensToCssConverter implements DesignTokensToCssConverterInterface
{
    private const GENERAL_SCOPE = '__general__';
    private const SCOPE_PREFIX = 'scope:';

    public function convert(array $designTokens): string
    {
        $rows = [];
        $this->appendBlock($rows, ':root', $designTokens);
        foreach ($designTokens as $scope => $componentGroups) {
            if (!is_array($componentGroups)) {
                continue;
            }
            if ($scope === self::GENERAL_SCOPE) {
                $this->appendComponents($rows, $componentGroups);
                continue;
            }
            if (str_starts_with((string) $scope, self::SCOPE_PREFIX)) {
                $this->appendComponents($rows, $componentGroups, substr($scope, strlen(self::SCOPE_PREFIX)));
            }
        }
        return implode("\n", $rows);
    }

    public function convertRootTokens(array $tokens, string $selector = ':root'): string
    {
        $rows = [];
        $this->appendBlock($rows, $selector, $tokens);
        return implode("\n", $rows);
    }

    private function appendBlock(array &$rows, string $selector, array $declarations): void
    {
        $declarations = ColorSchemeOverrides::filterSafeRootTokens($declarations);
        if ($declarations === []) {
            return;
        }
        $rows[] = $selector . ' {';
        foreach ($declarations as $token => $value) {
            $rows[] = $token . ': ' . $value . ';';
        }
        $rows[] = '}';
    }

    private function appendComponents(array &$rows, array $componentGroups, ?string $scopeName = null): void
    {
        foreach ($componentGroups as $component => $componentTokens) {
            if (!is_array($componentTokens)) {
                continue;
            }
            $selectorName = $scopeName ?? (string) $component;
            if (preg_match('/^[a-zA-Z0-9_-]+$/', $selectorName) !== 1) {
                continue;
            }
            $selector = $scopeName === null ? '.c-' . $selectorName : '[data-scope*="' . $selectorName . ';"]';
            $this->appendBlock($rows, $selector, $componentTokens);
        }
    }
}
