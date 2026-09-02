<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V60;

use WpService\Contracts\GetThemeMod;
use WpService\Contracts\SetThemeMod;

/**
 * Replaces persisted empty header width selections with the explicit standard width token.
 */
class MigrateEmptyHeaderContainerWidthToExplicitDefault
{
    private const TOKENS_SETTING = 'tokens';
    private const HEADER_COMPONENT = 'header';
    private const WIDTH_TOKEN = '--c-header--container-max-width';
    private const STANDARD_WIDTH = 'var(--container-width)';

    /**
     * @param GetThemeMod&SetThemeMod $wpService
     */
    public function __construct(
        private readonly GetThemeMod&SetThemeMod $wpService,
    ) {}

    public function migrate(): void
    {
        $tokens = $this->getTokens();
        $componentTokens = &$tokens['component'];

        if (!is_array($componentTokens)) {
            return;
        }

        $hasChanges = false;

        foreach ($componentTokens as $scope => &$scopeTokens) {
            if (!is_array($scopeTokens)) {
                continue;
            }

            $headerTokens = $scopeTokens[self::HEADER_COMPONENT] ?? null;

            if (!is_array($headerTokens) || !array_key_exists(self::WIDTH_TOKEN, $headerTokens)) {
                continue;
            }

            if (!is_string($headerTokens[self::WIDTH_TOKEN]) || trim($headerTokens[self::WIDTH_TOKEN]) !== '') {
                continue;
            }

            $scopeTokens[self::HEADER_COMPONENT][self::WIDTH_TOKEN] = self::STANDARD_WIDTH;
            $hasChanges = true;
        }
        unset($scopeTokens);

        if ($hasChanges) {
            $this->wpService->setThemeMod(self::TOKENS_SETTING, json_encode($tokens) ?: '');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getTokens(): array
    {
        $storedTokens = $this->wpService->getThemeMod(self::TOKENS_SETTING, null);

        if (is_array($storedTokens)) {
            return $storedTokens;
        }

        if (!is_string($storedTokens) || trim($storedTokens) === '') {
            return [];
        }

        $decodedTokens = json_decode($storedTokens, true);

        return is_array($decodedTokens) ? $decodedTokens : [];
    }
}
