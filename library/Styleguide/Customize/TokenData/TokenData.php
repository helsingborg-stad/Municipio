<?php

namespace Municipio\Styleguide\Customize\TokenData;

use Composer\InstalledVersions;
use Municipio\Styleguide\Customize\OverrideState\OverrideStateInterface;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;

class TokenData
{
    private const STYLEGUIDE_PACKAGE = 'helsingborg-stad/styleguide';

    public function __construct(
        private ApplyFilters&AddFilter $wpService,
        private OverrideStateInterface $overrideStateService,
    ) {}

    public function getTokenData(): array
    {
        if (!file_exists($this->getFilePath())) {
            return [];
        }

        $contents = file_get_contents($this->getFilePath());
        $contents = $contents === false ? [] : json_decode($contents, true);

        // Apply local decorators
        (new Decorators\AddOverrideFontFamilies($this->wpService, $this->overrideStateService))->addHooks();
        $contents = (new Decorators\FontFamilies($this->wpService))->decorate($contents);

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
