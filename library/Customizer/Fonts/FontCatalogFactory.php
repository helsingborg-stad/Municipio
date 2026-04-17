<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use WpService\WpService;

/**
 * Creates configured font catalog services.
 */
class FontCatalogFactory
{
    /**
     * @param WpService $wpService
     */
    public function __construct(
        private readonly WpService $wpService,
    ) {}

    /**
     * Creates a configured font catalog.
     *
     * @return FontCatalog
     */
    public function create(): FontCatalog
    {
        $fontRepository = $this->createFontRepository();

        return new FontCatalog(
            $this->wpService,
            $fontRepository,
            new GoogleFontsCssLocaleFilter($this->wpService),
            new FontStyleguideOptionProvider($this->wpService, $fontRepository),
            new FontCatalogMigrator($this->wpService, new ManagedFonts(), $this->createLegacyUploadedFontRepository()),
            new UploadedFontFacePrinter($this->wpService, $fontRepository),
        );
    }

    /**
     * Creates a repository for all uploaded fonts.
     *
     * @return FontRepository
     */
    public function createFontRepository(): FontRepository
    {
        return new FontRepository(
            $this->createManagedUploadedFontRepository(),
            $this->createLegacyUploadedFontRepository(),
        );
    }

    /**
     * Creates the managed uploaded font repository.
     *
     * @return ManagedUploadedFontRepository
     */
    private function createManagedUploadedFontRepository(): ManagedUploadedFontRepository
    {
        return new ManagedUploadedFontRepository(
            $this->wpService,
            $this->createUploadedFontMapper(),
        );
    }

    /**
     * Creates the legacy uploaded font repository.
     *
     * @return LegacyUploadedFontRepository
     */
    private function createLegacyUploadedFontRepository(): LegacyUploadedFontRepository
    {
        return new LegacyUploadedFontRepository(
            $this->wpService,
            $this->createUploadedFontMapper(),
        );
    }

    /**
     * Creates the uploaded font mapper.
     *
     * @return UploadedFontMapper
     */
    private function createUploadedFontMapper(): UploadedFontMapper
    {
        return new UploadedFontMapper($this->wpService);
    }
}
