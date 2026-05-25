<?php

namespace Municipio\Styleguide\Customize\ApplyStyles\DesignTokensToCssConverter;

/**
 * Converts design tokens to a CSS string.
 */
class DesignTokensToCssConverter implements DesignTokensToCssConverterInterface
{
    private const GENERAL_SCOPE = '__general__';
    private const SCOPE_PREFIX = 'scope:';

    /**
     * Converts design tokens to CSS.
     *
     * @param array<mixed> $designTokens Design tokens grouped by scope and component.
     *
     * @return string CSS generated from the design tokens.
     */
    public function convert(array $designTokens): string
    {
        if (empty($designTokens)) {
            return '';
        }

        $rows = [];
        $rootTokens = $this->getRootTokens($designTokens);

        if (!empty($rootTokens)) {
            $this->appendBlock($rows, ':root', $rootTokens);
        }

        foreach ($designTokens as $scope => $scopeTokens) {
            if (!is_string($scope) || !is_array($scopeTokens)) {
                continue;
            }

            if ($scope === self::GENERAL_SCOPE) {
                $this->appendComponentBlocks($rows, $scopeTokens);
            }

            if ($this->isScopedScope($scope)) {
                $scopeName = $this->extractScopeName($scope);
                $this->appendScopedComponentBlocks($rows, $scopeTokens, $scopeName);
            }
        }

        return implode("\n", $rows);
    }

    /**
     * Returns top-level root tokens (keys starting with --).
     *
     * @param array<mixed> $designTokens Full design token array.
     *
     * @return array<string, mixed>
     */
    private function getRootTokens(array $designTokens): array
    {
        return array_filter(
            $designTokens,
            static fn($key): bool => is_string($key) && str_starts_with($key, '--'),
            ARRAY_FILTER_USE_KEY,
        );
    }

    /**
     * Appends a CSS block with declarations.
     *
     * @param array<int, string> $rows Destination row collection.
     * @param array<string, mixed> $declarations Declaration list.
     */
    private function appendBlock(array &$rows, string $selector, array $declarations): void
    {
        $rows[] = sprintf('%s {', $selector);

        foreach ($declarations as $token => $value) {
            $rows[] = sprintf('%s: %s;', $token, (string) $value);
        }

        $rows[] = '}';
    }

    /**
     * Appends component-level blocks for each component token group.
     *
     * @param array<int, string> $rows Destination row collection.
     * @param array<mixed> $componentGroups Component token groups.
     */
    private function appendComponentBlocks(array &$rows, array $componentGroups): void
    {
        foreach ($componentGroups as $component => $componentTokens) {
            if (!is_string($component) || !is_array($componentTokens)) {
                continue;
            }

            $this->appendBlock($rows, sprintf('.c-%s', $component), $componentTokens);
        }
    }

    /**
     * Appends component blocks scoped to a data-scope selector.
     *
     * @param array<int, string> $rows Destination row collection.
     * @param array<mixed> $componentGroups Component token groups.
     */
    private function appendScopedComponentBlocks(array &$rows, array $componentGroups, string $scopeName): void
    {
        foreach ($componentGroups as $component => $componentTokens) {
            if (!is_string($component) || !is_array($componentTokens)) {
                continue;
            }

            $selector = sprintf('[data-scope*="%s;"]', $scopeName);
            $this->appendBlock($rows, $selector, $componentTokens);
        }
    }

    /**
     * Checks if a scope key is a scoped scope (scope:...).
     */
    private function isScopedScope(string $scope): bool
    {
        return str_starts_with($scope, self::SCOPE_PREFIX);
    }

    /**
     * Extracts scope name from a scope key.
     */
    private function extractScopeName(string $scope): string
    {
        return substr($scope, strlen(self::SCOPE_PREFIX));
    }
}
