<?php

namespace Municipio\Styleguide\Customize\ComponentData;

use Composer\InstalledVersions;
use WpService\Contracts\ApplyFilters;

class ComponentData
{
    private const STYLEGUIDE_PACKAGE = 'helsingborg-stad/styleguide';

    public function __construct(
        private ApplyFilters $wpService,
    ) {}

    public function getComponentData(): array
    {
        if (!file_exists($this->getFilePath())) {
            return [];
        }

        $contents = file_get_contents($this->getFilePath());
        $contents = $contents === false ? [] : json_decode($contents, true);

        return $this->wpService->applyFilters('Municipio/Styleguide/Customize/ComponentData', $contents);
    }

    private function getStyleguidePath(): string
    {
        return InstalledVersions::getInstallPath(self::STYLEGUIDE_PACKAGE) ?? null;
    }

    private function getFilePath(): string
    {
        return realpath($this->getStyleguidePath()) . '/component-design-tokens.json';
    }
}
