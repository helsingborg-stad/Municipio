<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ResolveAcfPostsManualSelectionLanguageTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        static::assertInstanceOf(ResolveAcfPostsManualSelectionLanguage::class, $this->getSut());
    }

    #[TestDox('addHooks() registers the ACF post object query filter when Polylang is active')]
    public function testAddHooksRegistersFilterWhenPolylangIsActive(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $sut = new ResolveAcfPostsManualSelectionLanguage(
            $wpService,
            static fn(): bool => true,
        );

        $sut->addHooks();

        static::assertSame(
            [['acf/fields/post_object/query/name=posts_data_posts', [$sut, 'makeManualPostsFieldLanguageAgnostic'], 20, 3]],
            $wpService->methodCalls['addFilter'],
        );
    }

    #[TestDox('addHooks() does not register filter when Polylang is unavailable')]
    public function testAddHooksDoesNotRegisterFilterWhenPolylangIsUnavailable(): void
    {
        $wpService = new FakeWpService([
            'addFilter' => true,
        ]);

        $sut = new ResolveAcfPostsManualSelectionLanguage(
            $wpService,
            static fn(): bool => false,
        );

        $sut->addHooks();

        static::assertArrayNotHasKey('addFilter', $wpService->methodCalls);
    }

    #[TestDox('makeManualPostsFieldLanguageAgnostic() sets empty lang for posts_data_posts field')]
    public function testMakeManualPostsFieldLanguageAgnosticSetsEmptyLangForManualField(): void
    {
        $sut = $this->getSut(
            static fn(): bool => true,
        );

        $args = [
            'post_type' => ['post', 'evenemang'],
            'lang' => 'sv',
        ];

        $field = [
            'name' => 'posts_data_posts',
        ];

        $result = $sut->makeManualPostsFieldLanguageAgnostic($args, $field, 13018);

        static::assertSame('', $result['lang']);
    }

    #[TestDox('makeManualPostsFieldLanguageAgnostic() leaves args unchanged for other fields')]
    public function testMakeManualPostsFieldLanguageAgnosticLeavesOtherFieldsUnchanged(): void
    {
        $sut = $this->getSut(
            static fn(): bool => true,
        );

        $args = [
            'post_type' => ['post', 'evenemang'],
            'lang' => 'sv',
        ];

        $field = [
            'name' => 'posts_data_child_of',
        ];

        $result = $sut->makeManualPostsFieldLanguageAgnostic($args, $field, 13018);

        static::assertSame($args, $result);
    }

    #[TestDox('makeManualPostsFieldLanguageAgnostic() leaves args unchanged when Polylang is unavailable')]
    public function testMakeManualPostsFieldLanguageAgnosticLeavesArgsUnchangedWhenPolylangIsUnavailable(): void
    {
        $sut = $this->getSut(
            static fn(): bool => false,
        );

        $args = [
            'post_type' => ['post', 'evenemang'],
            'lang' => 'sv',
        ];

        $field = [
            'name' => 'posts_data_posts',
        ];

        $result = $sut->makeManualPostsFieldLanguageAgnostic($args, $field, 13018);

        static::assertSame($args, $result);
    }

    /**
     * Gets the system under test.
     *
     * @param ?Closure $polylangIsActiveResolver Optional Polylang availability resolver.
     *
     * @return ResolveAcfPostsManualSelectionLanguage
     */
    private function getSut(?Closure $polylangIsActiveResolver = null): ResolveAcfPostsManualSelectionLanguage
    {
        return new ResolveAcfPostsManualSelectionLanguage(
            new FakeWpService([
                'addFilter' => true,
            ]),
            $polylangIsActiveResolver,
        );
    }
}
