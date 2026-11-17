<?php

declare(strict_types=1);

namespace Municipio\Content\Images;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ImageAlignmentTest extends TestCase
{
    #[TestDox('replaceAlignmentClasses() makes left and right aligned images float')]
    public function testReplaceAlignmentClassesMakesLeftAndRightAlignedImagesFloat(): void
    {
        $left  = ImageAlignment::replaceAlignmentClasses('<img class="size-thumbnail alignleft">');
        $right = ImageAlignment::replaceAlignmentClasses('<img class="size-thumbnail alignright">');

        $this->assertStringContainsString('u-float--left', $left);
        $this->assertStringContainsString('u-float--none@xs', $left);
        $this->assertStringContainsString('u-margin__right--3', $left);
        $this->assertStringNotContainsString('alignleft', $left);

        $this->assertStringContainsString('u-float--right', $right);
        $this->assertStringContainsString('u-float--none@xs', $right);
        $this->assertStringContainsString('u-margin__left--3', $right);
        $this->assertStringNotContainsString('alignright', $right);
    }

    #[TestDox('replaceAlignmentClasses() centers aligncenter and removes alignnone')]
    public function testReplaceAlignmentClassesCentersAndRemovesNone(): void
    {
        $this->assertSame(
            '<img class="u-margin__y--2 u-margin__x--auto u-text-align--center">',
            ImageAlignment::replaceAlignmentClasses('<img class="aligncenter">')
        );
        $this->assertSame(
            '<img class="size-full ">',
            ImageAlignment::replaceAlignmentClasses('<img class="size-full alignnone">')
        );
    }

    #[TestDox('replaceAlignmentClasses() is idempotent')]
    public function testReplaceAlignmentClassesIsIdempotent(): void
    {
        $once  = ImageAlignment::replaceAlignmentClasses('<img class="alignright">');
        $twice = ImageAlignment::replaceAlignmentClasses($once);

        $this->assertSame($once, $twice);
    }

    #[TestDox('getAlignment() detects alignment from WordPress and utility classes')]
    #[DataProvider('alignmentProvider')]
    public function testGetAlignment(array $classes, ?string $expected): void
    {
        $this->assertSame($expected, ImageAlignment::getAlignment($classes));
    }

    public static function alignmentProvider(): array
    {
        return [
            'alignleft'              => [['wp-image-1', 'alignleft'], ImageAlignment::LEFT],
            'alignright'             => [['alignright'], ImageAlignment::RIGHT],
            'aligncenter'            => [['aligncenter'], ImageAlignment::CENTER],
            'alignnone'              => [['alignnone'], ImageAlignment::NONE],
            'already replaced left'  => [['u-float--left', 'u-float--none@xs'], ImageAlignment::LEFT],
            'already replaced right' => [['u-float--right'], ImageAlignment::RIGHT],
            'no alignment'           => [['size-thumbnail', 'wp-image-1'], null],
            'empty'                  => [[], null],
        ];
    }

    #[TestDox('resolveClassList() moves alignment from image to wrapper when the wrapper has none (image without caption)')]
    public function testResolveClassListUsesImageAlignmentWhenWrapperHasNone(): void
    {
        $classes = ImageAlignment::resolveClassList(
            [],
            ['wp-image-4137580', 'size-thumbnail', 'alignright']
        );

        $this->assertContains('u-float--right', $classes);
        $this->assertContains('u-float--none@xs', $classes);
        $this->assertNotContains('wp-image-4137580', $classes);
        $this->assertNotContains('size-thumbnail', $classes);
    }

    #[TestDox('resolveClassList() handles image classes that already have been replaced')]
    public function testResolveClassListHandlesAlreadyReplacedImageClasses(): void
    {
        $imageClasses = ImageAlignment::splitClassAttribute(
            ImageAlignment::replaceAlignmentClasses('c-image__image wp-image-1 size-thumbnail alignleft')
        );

        $classes = ImageAlignment::resolveClassList([], $imageClasses);

        $this->assertContains('u-float--left', $classes);
    }

    #[TestDox('resolveClassList() keeps wrapper alignment when present (image with caption)')]
    public function testResolveClassListKeepsWrapperAlignment(): void
    {
        $wrapper = ['wp-caption', 'alignleft'];

        $this->assertSame(
            $wrapper,
            ImageAlignment::resolveClassList($wrapper, ['wp-image-1', 'alignright'])
        );
    }

    #[TestDox('resolveClassList() keeps wrapper classes unchanged when the image has no alignment')]
    public function testResolveClassListWithoutAlignment(): void
    {
        $this->assertSame(
            ['some-class'],
            ImageAlignment::resolveClassList(['some-class', ''], ['wp-image-1', 'size-full'])
        );
    }

    #[TestDox('resolveClassList() does not add classes for alignnone')]
    public function testResolveClassListWithAlignNone(): void
    {
        $this->assertSame([], ImageAlignment::resolveClassList([], ['alignnone']));
    }

    #[TestDox('splitClassAttribute() splits on any whitespace and drops empty values')]
    public function testSplitClassAttribute(): void
    {
        $this->assertSame(['a', 'b', 'c'], ImageAlignment::splitClassAttribute("  a  b\n\tc "));
        $this->assertSame([], ImageAlignment::splitClassAttribute(null));
        $this->assertSame([], ImageAlignment::splitClassAttribute(''));
    }
}
