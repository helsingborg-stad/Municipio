<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use Municipio\Customizer\Fonts\FontCatalogFactory;
use Municipio\Customizer\Fonts\NativeFontLibraryRepository;
use Municipio\Upgrade\VersionInterface;
use WpService\WpService;

/**
 * Class Version42
 */
class Version42 implements VersionInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly WpService $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function upgradeToVersion(): void
    {
        $fontCatalogFactory = new FontCatalogFactory($this->wpService);

        (new MigrateFontsToNativeFontLibrary(
            $this->wpService,
            $fontCatalogFactory->createFontRepository(),
            new NativeFontLibraryRepository(),
        ))->migrate();
    }
}
