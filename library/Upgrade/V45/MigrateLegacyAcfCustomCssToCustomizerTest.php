<?php

namespace Municipio\Upgrade\V45;

use AcfService\Contracts\GetField;
use AcfService\Contracts\UpdateField;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\WpGetCustomCss;
use WpService\Contracts\WpUpdateCustomCssPost;

class MigrateLegacyAcfCustomCssToCustomizerTest extends TestCase
{
    #[TestDox('appends legacy ACF custom CSS to native Customizer CSS and clears the legacy field')]
    public function testMigrateAppendsLegacyAcfCustomCssToNativeCustomizerCssAndClearsLegacyField(): void
    {
        $wpService = new MigrateLegacyAcfCustomCssToCustomizerWpServiceFake('body { color: red; }');
        $acfService = new MigrateLegacyAcfCustomCssToCustomizerAcfServiceFake('.site-header { color: blue; }');

        (new MigrateLegacyAcfCustomCssToCustomizer($wpService, $acfService))->migrate();

        $this->assertSame("body { color: red; }\n\n.site-header { color: blue; }", $wpService->updatedCss);
        $this->assertSame('', $acfService->updatedValue);
        $this->assertSame('custom_css_input', $acfService->updatedSelector);
        $this->assertSame('option', $acfService->updatedPostId);
    }

    #[TestDox('uses legacy CSS as native Customizer CSS when the native editor is empty')]
    public function testMigrateUsesLegacyCssWhenNativeCustomizerCssIsEmpty(): void
    {
        $wpService = new MigrateLegacyAcfCustomCssToCustomizerWpServiceFake('');
        $acfService = new MigrateLegacyAcfCustomCssToCustomizerAcfServiceFake('.site-footer { color: green; }');

        (new MigrateLegacyAcfCustomCssToCustomizer($wpService, $acfService))->migrate();

        $this->assertSame('.site-footer { color: green; }', $wpService->updatedCss);
        $this->assertSame('', $acfService->updatedValue);
    }

    #[TestDox('does not update native Customizer CSS when legacy ACF CSS is empty')]
    public function testMigrateDoesNotUpdateNativeCustomizerCssWhenLegacyAcfCssIsEmpty(): void
    {
        $wpService = new MigrateLegacyAcfCustomCssToCustomizerWpServiceFake('body { color: red; }');
        $acfService = new MigrateLegacyAcfCustomCssToCustomizerAcfServiceFake('');

        (new MigrateLegacyAcfCustomCssToCustomizer($wpService, $acfService))->migrate();

        $this->assertNull($wpService->updatedCss);
        $this->assertNull($acfService->updatedValue);
    }

    #[TestDox('clears legacy field without duplicating CSS that already exists in the native editor')]
    public function testMigrateClearsLegacyFieldWithoutDuplicatingCssThatAlreadyExistsInNativeEditor(): void
    {
        $wpService = new MigrateLegacyAcfCustomCssToCustomizerWpServiceFake("body { color: red; }\n\n.site-header { color: blue; }");
        $acfService = new MigrateLegacyAcfCustomCssToCustomizerAcfServiceFake('.site-header { color: blue; }');

        (new MigrateLegacyAcfCustomCssToCustomizer($wpService, $acfService))->migrate();

        $this->assertSame("body { color: red; }\n\n.site-header { color: blue; }", $wpService->updatedCss);
        $this->assertSame('', $acfService->updatedValue);
    }
}

class MigrateLegacyAcfCustomCssToCustomizerWpServiceFake implements WpGetCustomCss, WpUpdateCustomCssPost
{
    public ?string $updatedCss = null;

    public function __construct(
        private readonly string $customCss,
    ) {}

    public function wpGetCustomCss(string $stylesheet = ''): string
    {
        return $this->customCss;
    }

    public function wpUpdateCustomCssPost(string $css, array $args = []): \WP_Post|\WP_Error
    {
        $this->updatedCss = $css;

        return new \WP_Error();
    }
}

class MigrateLegacyAcfCustomCssToCustomizerAcfServiceFake implements GetField, UpdateField
{
    public ?string $updatedSelector = null;
    public mixed $updatedValue = null;
    public mixed $updatedPostId = null;

    public function __construct(
        private readonly mixed $fieldValue,
    ) {}

    public function getField(
        string $selector,
        int|false|string $postId = false,
        bool $formatValue = true,
        bool $escapeHtml = false,
    ) {
        return $this->fieldValue;
    }

    public function updateField(string $selector, mixed $value, mixed $postId = false): bool
    {
        $this->updatedSelector = $selector;
        $this->updatedValue = $value;
        $this->updatedPostId = $postId;

        return true;
    }
}
