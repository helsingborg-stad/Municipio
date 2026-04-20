<?php

namespace Municipio\Styleguide\Customize\TokenData;

use Composer\InstalledVersions;
use Municipio\Styleguide\Customize\OverrideState\OverrideStateInterface;
use WpService\Contracts\__;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;

class TokenData
{
    private const STYLEGUIDE_PACKAGE = 'helsingborg-stad/styleguide';

    public function __construct(
        private ApplyFilters&AddFilter&__ $wpService,
        private OverrideStateInterface $overrideStateService,
    ) {}

    public function getTokenData(): array
    {
        if (!file_exists(static::getFilePath())) {
            return [];
        }

        $contents = file_get_contents(static::getFilePath());
        $contents = $contents === false ? [] : json_decode($contents, true);

        // Apply local decorators
        $contents = (new Decorators\ApplyI18n($this->wpService))->decorate($contents);
        (new Decorators\AddOverrideFontFamilies($this->wpService, $this->overrideStateService))->addHooks();
        $contents = (new Decorators\FontFamilies($this->wpService))->decorate($contents);

        return $this->wpService->applyFilters('Municipio/Styleguide/Customize/TokenData', $contents);
    }

    private static function getStyleguidePath(): string
    {
        return InstalledVersions::getInstallPath(self::STYLEGUIDE_PACKAGE) ?? null;
    }

    public static function getFilePath(): string
    {
        return realpath(static::getStyleguidePath()) . '/source/data/design-tokens.json';
    }
}
