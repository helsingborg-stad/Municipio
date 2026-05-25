<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests font catalog service construction.
 */
class FontCatalogFactoryTest extends TestCase
{
    #[TestDox('create() returns a configured font catalog')]
    public function testCreateReturnsAConfiguredFontCatalog(): void
    {
        $factory = new FontCatalogFactory(new FakeWpService());

        static::assertInstanceOf(FontCatalog::class, $factory->create());
    }

    #[TestDox('createFontRepository() returns an uploaded font repository')]
    public function testCreateFontRepositoryReturnsAnUploadedFontRepository(): void
    {
        $factory = new FontCatalogFactory(new FakeWpService());

        static::assertInstanceOf(FontRepository::class, $factory->createFontRepository());
    }
}
