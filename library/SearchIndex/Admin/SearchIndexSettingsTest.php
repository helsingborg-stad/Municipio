<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin;

use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\AdminNotices\AdminNoticesInterface;
use Municipio\Helper\AdminNotices\AdminNoticeType;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;
use Override;
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
            static::createAdminNoticesService()
        );

        $field = $settings->addAttachmentMimeTypeChoices(['choices' => []]);

        static::assertSame([
            'image/jpeg' => 'JPG, JPEG (image/jpeg)',
            'application/pdf' => 'PDF (application/pdf)',
        ], $field['choices']);
    }

    private static function createAdminNoticesService(): AdminNoticesInterface {
        return new class implements AdminNoticesInterface {
            public function addNotice(string $message, AdminNoticeType $type = AdminNoticeType::INFO, bool $dismissible = true): void
            {
            }
        };
    }
}