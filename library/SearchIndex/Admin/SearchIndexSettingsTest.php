<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin;

use AcfService\Implementations\FakeAcfService;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class SearchIndexSettingsTest extends TestCase
{
    public function testAddsReadableUniqueAttachmentMimeTypeChoices(): void
    {
        $wpService = new FakeWpService([
            'getAllowedMimeTypes' => [
                'jpg|jpeg' => 'image/jpeg',
                'jpe' => 'image/jpeg',
                'pdf' => 'application/pdf',
            ],
        ]);
        $acfService = new FakeAcfService();
        $config = new SearchIndexConfig($acfService);
        $settings = new SearchIndexSettings(
            $wpService,
            $acfService,
            $config,
            new SearchProviderFactory($wpService, $config),
        );

        $field = $settings->addAttachmentMimeTypeChoices(['choices' => []]);

        static::assertSame([
            'image/jpeg' => 'JPG, JPEG (image/jpeg)',
            'application/pdf' => 'PDF (application/pdf)',
        ], $field['choices']);
    }
}