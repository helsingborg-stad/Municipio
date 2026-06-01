<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Coordinates font catalog hooks.
 */
class FontCatalog implements Hookable
{
    public const GOOGLE_FONTS_SETTING = 'municipio_font_catalog_google_fonts';
    public const UPLOADED_FONTS_SETTING = 'municipio_font_catalog_uploaded_fonts';
    public const MIGRATION_SETTING = 'municipio_font_catalog_migrated';

    /**
     * @param WpService $wpService
     * @param FontRepository $fontRepository
     * @param FontStyleguideOptionProvider $fontStyleguideOptionProvider
     */
    public function __construct(
        private readonly WpService $wpService,
        private readonly FontRepository $fontRepository,
        private readonly FontStyleguideOptionProvider $fontStyleguideOptionProvider,
    ) {}

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('upload_mimes', [$this->fontRepository, 'addFontMimes'], 1, 1);
        $this->wpService->addFilter('Municipio/Styleguide/Customize/TokenData/FontFamilies', [$this, 'addStyleguideFontFamilies'], 10, 1);
    }

    /**
     * Adds managed font catalog families to styleguide token options.
     *
     * @param array<int, array{value: string, label: string}> $options
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function addStyleguideFontFamilies(array $options): array
    {
        return $this->fontStyleguideOptionProvider->addFontFamilies($options);
    }

}
