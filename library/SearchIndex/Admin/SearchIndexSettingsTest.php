<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Admin;

use AcfService\Implementations\FakeAcfService;
use Municipio\Helper\AdminNotices\AdminNoticesInterface;
use Municipio\Helper\AdminNotices\AdminNoticeType;
use Municipio\Helper\Constant\FakeConstant;
use Municipio\SearchIndex\Config\SearchIndexConfig;
use Municipio\SearchIndex\Provider\SearchProviderFactory;
use PHPUnit\Framework\Attributes\TestDox;
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

    /**
     * Verify the provider field shows the effective constant-backed selection.
     */
    public function testLoadsEffectiveProviderValueWhenOverriddenByConstant(): void
    {
        $wpService = new FakeWpService();
        $acfService = new FakeAcfService([
            'getField' => static fn(string $selector): string => $selector === 'search_index_provider' ? 'algolia' : '',
        ]);
        $config = new SearchIndexConfig($acfService, new FakeConstant([
            'SEARCH_INDEX_PROVIDER' => 'typesense',
        ]));
        $settings = new SearchIndexSettings(
            $wpService,
            $acfService,
            $config,
            new SearchProviderFactory($wpService, $config),
            static::createAdminNoticesService()
        );

        $value = call_user_func([$settings, 'loadConstantOverrideValue'], 'algolia', 'option', [
            'name' => 'search_index_provider',
        ]);

        static::assertSame('typesense', $value);
    }

    /**
     * Verify other constant-backed fields also show their effective value.
     */
    public function testLoadsEffectiveConstantBackedFieldValue(): void
    {
        $wpService = new FakeWpService();
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

        $value = call_user_func([$settings, 'loadConstantOverrideValue'], '', 'option', [
            'name' => 'search_index_typesense_api_key',
        ]);

        static::assertSame(implode('-', ['server', 'api', 'key']), $value);
    }

    #[TestDox('options page is not registered when user cannot manage options')]
    public function testDoesNotRegisterOptionsPageIfUserCannotManageOptions(): void
    {
        $wpService = new FakeWpService(['currentUserCan' => false, '__' => static fn(string $text): string => $text]);
        $acfService = new FakeAcfService();
        $config = new SearchIndexConfig($acfService);
        $settings = new SearchIndexSettings( $wpService, $acfService, $config, new SearchProviderFactory($wpService, $config), static::createAdminNoticesService() );

        $settings->registerOptionsPage();

        static::assertArrayNotHasKey('addOptionsPage', $acfService->methodCalls);
    }

    #[TestDox('options page is registered when user can manage options')]
    public function testRegistersOptionsPageIfUserCanManageOptions(): void
    {
        $wpService = new FakeWpService([ 'currentUserCan' => true, '__' => static fn(string $text): string => $text, ]);
        $acfService = new FakeAcfService();
        $config = new SearchIndexConfig($acfService);
        $settings = new SearchIndexSettings( $wpService, $acfService, $config, new SearchProviderFactory($wpService, $config), static::createAdminNoticesService() );

        $settings->registerOptionsPage();

        static::assertSame('manage_options', $wpService->methodCalls['currentUserCan'][0][0]);
        static::assertSame('municipio-search-index-settings', $acfService->methodCalls['addOptionsPage'][0][0]['menu_slug']);
    }

    private static function createAdminNoticesService(): AdminNoticesInterface
    {
        return new class implements AdminNoticesInterface {
            public function addNotice(string $message, AdminNoticeType $type = AdminNoticeType::INFO, bool $dismissible = true): void
            {
            }
        };
    }
}