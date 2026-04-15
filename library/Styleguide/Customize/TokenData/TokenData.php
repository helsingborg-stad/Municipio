<?php

namespace Municipio\Styleguide\Customize\TokenData;

use Composer\InstalledVersions;
use WpService\Contracts\ApplyFilters;

class TokenData
{
    private const STYLEGUIDE_PACKAGE = 'helsingborg-stad/styleguide';

    public function __construct(
        private ApplyFilters $wpService,
    ) {}

    public function getTokenData(): array
    {
        if (!file_exists($this->getFilePath())) {
            return [];
        }

        $contents = file_get_contents($this->getFilePath());
        $contents = $contents === false ? [] : json_decode($contents, true);

        return $this->wpService->applyFilters('Municipio/Styleguide/Customize/TokenData', $contents);
    }

    private function getStyleguidePath(): string
    {
        return InstalledVersions::getInstallPath(self::STYLEGUIDE_PACKAGE) ?? null;
    }

    private function getFilePath(): string
    {
        return realpath($this->getStyleguidePath()) . '/source/data/design-tokens.json';
    }
}
