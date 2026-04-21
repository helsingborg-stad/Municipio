<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ComponentData;

use Composer\InstalledVersions;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\_x;

class ComponentData
{
    private const STYLEGUIDE_PACKAGE = 'helsingborg-stad/styleguide';

    public function __construct(
        private ApplyFilters&_x $wpService,
    ) {}

    public function getComponentData(): array
    {
        if (!file_exists(static::getFilePath())) {
            return [];
        }

        $contents = file_get_contents(static::getFilePath());
        $contents = $contents === false ? [] : json_decode($contents, true);

        // Apply local decorators
        $contents = (new Decorators\ApplyI18n($this->wpService))->decorate($contents);

        return $this->wpService->applyFilters('Municipio/Styleguide/Customize/ComponentData', $contents);
    }

    private static function getStyleguidePath(): string
    {
        return InstalledVersions::getInstallPath(self::STYLEGUIDE_PACKAGE) ?? null;
    }

    public static function getFilePath(): string
    {
        return realpath(static::getStyleguidePath()) . '/component-design-tokens.json';
    }
}
