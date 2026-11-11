<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ApplyStyles;

use Municipio\Styleguide\Customize\TokenData\ColorTokenDefinitions;

/** Rebinds derived and component aliases where Gutenberg scopes its colours. */
class EditorColorTokens
{
    private ?array $componentTokens = null;

    public function __construct(
        private ?string $componentTokensPath = null,
    ) {}

    public function getCss(array $rootTokens, string $selector): string
    {
        if ($selector === '' || $rootTokens === []) {
            return '';
        }
        return (new DesignTokensToCssConverter\DesignTokensToCssConverter())->convertRootTokens($this->getTokens($rootTokens), $selector);
    }

    /** @param array<string, string> $overrides */
    public function getTokens(array $overrides): array
    {
        $derivedTokens = [];
        $aliases = [];
        foreach (ColorTokenDefinitions::getSettings() as $setting) {
            $variable = $setting['variable'];
            $defaultValue = $setting['default'] ?? null;
            if (is_string($defaultValue) && str_contains($defaultValue, 'var(')) {
                $derivedTokens[$variable] = $defaultValue;
            }
            $aliases['--u-color--color-' . substr($variable, strlen('--color--'))] = 'var(' . $variable . ')';
        }
        foreach ($overrides as $variable => $value) {
            if (!str_starts_with($variable, '--color--')) {
                continue;
            }
            $aliases['--u-color--color-' . substr($variable, strlen('--color--'))] = 'var(' . $variable . ')';
        }
        return array_merge($derivedTokens, $overrides, $this->getComponentTokens(), $aliases);
    }

    private function getComponentTokens(): array
    {
        if ($this->componentTokens !== null) {
            return $this->componentTokens;
        }
        $path = $this->componentTokensPath ?? dirname(__DIR__, 4) . '/assets/dist/styleguide/editor-color-tokens.json';
        $contents = is_file($path) ? file_get_contents($path) : false;
        $tokens = $contents === false ? null : json_decode($contents, true);
        return $this->componentTokens = ColorSchemeOverrides::filterSafeRootTokens(is_array($tokens) ? $tokens : []);
    }
}
