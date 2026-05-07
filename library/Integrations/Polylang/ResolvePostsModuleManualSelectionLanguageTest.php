<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolvePostsModuleManualSelectionLanguageTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolvePostsModuleManualSelectionLanguage::class, $this->getSut());
    }

    #[TestDox('addHooks() registers Posts module args filter when Polylang is active')]
    public function testAddHooksRegistersFilterWhenPolylangIsActive(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $sut = new ResolvePostsModuleManualSelectionLanguage(
            $wpService,
            static fn(): bool => true,
        );

        $sut->addHooks();

        static::assertSame(
            [
                [
                    'Modularity/Module/Posts/GetPosts/Args',
                    [$sut, 'makeManualPostsQueryLanguageAgnostic'],
                    20,
                    4,
                ],
            ],
            $wpService->methodCalls['addFilter'],
        );
    }

    #[TestDox('addHooks() does not register Posts module args filter when Polylang is unavailable')]
    public function testAddHooksDoesNotRegisterFilterWhenPolylangIsUnavailable(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $sut = new ResolvePostsModuleManualSelectionLanguage(
            $wpService,
            static fn(): bool => false,
        );

        $sut->addHooks();

        static::assertArrayNotHasKey('addFilter', $wpService->methodCalls);
    }

    #[TestDox('makeManualPostsQueryLanguageAgnostic() sets empty lang for manual source')]
    public function testMakeManualPostsQueryLanguageAgnosticSetsEmptyLangForManualSource(): void
    {
        $sut = $this->getSut(static fn(): bool => true);

        $args = ['post__in' => [403, 407], 'lang' => 'sv'];
        $fields = ['posts_data_source' => 'manual'];

        $result = $sut->makeManualPostsQueryLanguageAgnostic($args, $fields, 1, []);

        static::assertSame('', $result['lang']);
    }

    #[TestDox('makeManualPostsQueryLanguageAgnostic() keeps args unchanged for non-manual source')]
    public function testMakeManualPostsQueryLanguageAgnosticKeepsArgsForNonManualSource(): void
    {
        $sut = $this->getSut(static fn(): bool => true);

        $args = ['post_type' => ['post'], 'lang' => 'sv'];
        $fields = ['posts_data_source' => 'posttype'];

        $result = $sut->makeManualPostsQueryLanguageAgnostic($args, $fields, 1, []);

        static::assertSame($args, $result);
    }

    #[TestDox('makeManualPostsQueryLanguageAgnostic() keeps args unchanged when Polylang is unavailable')]
    public function testMakeManualPostsQueryLanguageAgnosticKeepsArgsWhenPolylangIsUnavailable(): void
    {
        $sut = $this->getSut(static fn(): bool => false);

        $args = ['post__in' => [403, 407], 'lang' => 'sv'];
        $fields = ['posts_data_source' => 'manual'];

        $result = $sut->makeManualPostsQueryLanguageAgnostic($args, $fields, 1, []);

        static::assertSame($args, $result);
    }

    /**
     * Gets the system under test.
     */
    private function getSut(?Closure $polylangIsActiveResolver = null): ResolvePostsModuleManualSelectionLanguage
    {
        return new ResolvePostsModuleManualSelectionLanguage(
            new FakeWpService([
                'addFilter' => true,
            ]),
            $polylangIsActiveResolver,
        );
    }
}
