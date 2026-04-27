<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveLanguageMenuItemsTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolveLanguageMenuItems::class, $this->getSut());
    }

    #[TestDox('addHooks() registers Municipio navigation items filter')]
    public function testAddHooksRegistersNavigationItemsFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);
        $sut       = new ResolveLanguageMenuItems($wpService);

        $sut->addHooks();

        static::assertSame(
            [['Municipio/Navigation/Items', [$sut, 'populateLanguageMenuItems'], 10, 2]],
            $wpService->methodCalls['addFilter']
        );
    }

    #[TestDox('populateLanguageMenuItems() returns unchanged items for non-language identifiers')]
    public function testReturnsUnchangedItemsForNonLanguageIdentifier(): void
    {
        $sut       = $this->getSut(languagesResolver: static fn () => [['id' => 1, 'name' => 'English', 'url' => 'http://example.com/en/', 'current_lang' => true]]);
        $menuItems = [['id' => 99, 'label' => 'Some page']];

        static::assertSame($menuItems, $sut->populateLanguageMenuItems($menuItems, 'primary'));
    }

    #[TestDox('populateLanguageMenuItems() does not override existing language menu items')]
    public function testDoesNotOverrideExistingItems(): void
    {
        $sut       = $this->getSut(languagesResolver: static fn () => [['id' => 1, 'name' => 'English', 'url' => 'http://example.com/en/', 'current_lang' => true]]);
        $menuItems = [['id' => 42, 'label' => 'Manually configured']];

        static::assertSame($menuItems, $sut->populateLanguageMenuItems($menuItems, 'language'));
    }

    #[TestDox('populateLanguageMenuItems() maps Polylang languages to menu items')]
    public function testMapsPolylangLanguagesToMenuItems(): void
    {
        $sut = $this->getSut(
            languagesResolver: static fn () => [
                ['id' => 1, 'slug' => 'en', 'name' => 'English', 'url' => 'http://example.com/en/', 'current_lang' => false],
                ['id' => 2, 'slug' => 'sv', 'name' => 'Svenska', 'url' => 'http://example.com/sv/', 'current_lang' => true],
            ]
        );

        $result = $sut->populateLanguageMenuItems([], 'language');

        static::assertCount(2, $result);
        static::assertSame('English', $result[0]['label']);
        static::assertSame('http://example.com/en/', $result[0]['href']);
        static::assertFalse($result[0]['active']);
        static::assertSame('Svenska', $result[1]['label']);
        static::assertSame('http://example.com/sv/', $result[1]['href']);
        static::assertTrue($result[1]['active']);
    }

    #[TestDox('populateLanguageMenuItems() returns empty array when no languages are resolved')]
    public function testReturnsEmptyWhenNoLanguagesResolved(): void
    {
        $sut    = $this->getSut(languagesResolver: static fn () => []);
        $result = $sut->populateLanguageMenuItems([], 'language');

        static::assertSame([], $result);
    }

    #[TestDox('populateLanguageMenuItems() returns unchanged items when Polylang is not available')]
    public function testReturnsUnchangedWhenPolylangNotAvailable(): void
    {
        $sut    = $this->getSut(languagesResolver: null);
        $result = $sut->populateLanguageMenuItems([], 'language');

        static::assertSame([], $result);
    }

    /**
     * Get the system under test.
     *
     * @param ?Closure $languagesResolver Optional Polylang languages resolver.
     *
     * @return ResolveLanguageMenuItems
     */
    private function getSut(?Closure $languagesResolver = null): ResolveLanguageMenuItems
    {
        return new ResolveLanguageMenuItems(
            new FakeWpService(['addFilter' => true]),
            $languagesResolver
        );
    }
}
