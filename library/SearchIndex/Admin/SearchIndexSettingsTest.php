<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin;

use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\AdminNotices\AdminNoticesInterface;
use Municipio\Helper\AdminNotices\AdminNoticeType;
use Municipio\Helper\Constant\FakeConstant;
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
            static::createAdminNoticesService()
        );

        $field = $settings->addAttachmentMimeTypeChoices(['choices' => []]);

        static::assertSame([
            'image/jpeg' => 'JPG, JPEG (image/jpeg)',
            'application/pdf' => 'PDF (application/pdf)',
        ], $field['choices']);
    }

    /**
     * Verify a field is disabled without exposing the overriding value.
     */
    public function testDisablesFieldOverriddenByConstant(): void
    {
        $wpService = new FakeWpService([
            '__' => static fn(string $text): string => $text,
        ]);
        $acfService = new FakeAcfService();
        $config = new SearchIndexConfig($acfService, new FakeConstant([
            'SEARCH_INDEX_TYPESENSE_API_KEY' => implode('-', ['server', 'api', 'key']),
        ]));
        $settings = new SearchIndexSettings(
            $wpService,
            $acfService,
            $config,
            new SearchProviderFactory($wpService, $config),
            static::createAdminNoticesService()
        );

        $field = $settings->disableConstantOverrideField([
            'name' => 'search_index_typesense_api_key',
            'instructions' => 'Existing instructions.',
        ]);

        static::assertSame(1, $field['disabled']);
        static::assertSame(1, $field['readonly']);
        static::assertSame(
            'Existing instructions. This field is disabled because SEARCH_INDEX_TYPESENSE_API_KEY is defined.',
            $field['instructions']
        );
    }

    /**
     * Verify an empty constant does not disable its associated field.
     */
    public function testLeavesFieldWithoutConstantOverrideEnabled(): void
    {
        $wpService = new FakeWpService();
        $acfService = new FakeAcfService();
        $config = new SearchIndexConfig($acfService, new FakeConstant([
            'SEARCH_INDEX_PROVIDER' => '',
        ]));
        $settings = new SearchIndexSettings(
            $wpService,
            $acfService,
            $config,
            new SearchProviderFactory($wpService, $config),
            static::createAdminNoticesService()
        );
        $field = [
            'name' => 'search_index_provider',
            'instructions' => 'Existing instructions.',
        ];

        static::assertSame($field, $settings->disableConstantOverrideField($field));
    }

    private static function createAdminNoticesService(): AdminNoticesInterface {
        return new class implements AdminNoticesInterface {
            public function addNotice(string $message, AdminNoticeType $type = AdminNoticeType::INFO, bool $dismissible = true): void
            {
            }
        };
    }
}