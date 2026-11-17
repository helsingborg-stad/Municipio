<?php

declare(strict_types=1);

namespace Municipio\Content\Images;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/*
 * Stubs for the WordPress functions used by ImagesDeprecated. They are declared in the
 * namespace of the class under test, so they take precedence over the global functions.
 */
if (!function_exists(__NAMESPACE__ . '\\add_action')) {
    function add_action(): bool
    {
        return true;
    }
}

if (!function_exists(__NAMESPACE__ . '\\add_filter')) {
    function add_filter(): bool
    {
        return true;
    }
}

if (!function_exists(__NAMESPACE__ . '\\attachment_url_to_postid')) {
    function attachment_url_to_postid(): int
    {
        return 0;
    }
}

if (!function_exists(__NAMESPACE__ . '\\apply_filters')) {
    function apply_filters(...$args)
    {
        return $args[1] ?? null;
    }
}

if (!function_exists(__NAMESPACE__ . '\\wp_strip_all_tags')) {
    function wp_strip_all_tags($text): string
    {
        return trim(strip_tags((string) $text));
    }
}

if (!function_exists(__NAMESPACE__ . '\\render_blade_view')) {
    /**
     * Minimal stand-in for the image component: a figure with the class list and caption.
     */
    function render_blade_view(...$args): string
    {
        $data = $args[1];

        return sprintf(
            '<figure class="c-image %s"><img src="%s" alt="%s">%s</figure>',
            implode(' ', $data['classList']),
            $data['src'],
            $data['alt'],
            $data['caption'] ? '<figcaption>' . $data['caption'] . '</figcaption>' : ''
        );
    }
}

class ImagesDeprecatedTest extends TestCase
{
    private function normalize(string $content): string
    {
        return (new ImagesDeprecated())->normalizeImages($content);
    }

    private function loadBody(string $html): \DOMXPath
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?><div id="root">' . $html . '</div>', LIBXML_NOERROR);

        return new \DOMXPath($dom);
    }

    #[TestDox('normalizeImages() moves the alignment of an image without caption to the rendered component')]
    public function testImageWithoutCaptionGetsFloatClasses(): void
    {
        $xpath = $this->loadBody($this->normalize(
            '<p><img class="wp-image-1 size-thumbnail alignright" src="https://example.com/a.png" alt="A" width="150"></p>'
        ));

        $figures = $xpath->query('//figure');

        $this->assertSame(1, $figures->length);
        $this->assertStringContainsString('u-float--right', $figures->item(0)->getAttribute('class'));
    }

    #[TestDox('normalizeImages() unwraps the caption figure so that aligned images with caption are not nested')]
    public function testCaptionFigureIsUnwrapped(): void
    {
        $xpath = $this->loadBody($this->normalize(
            '<figure id="attachment_3" class="wp-caption alignright" aria-describedby="caption-attachment-3">'
            . '<img class="wp-image-3 size-medium" src="https://example.com/a.png" alt="A" width="300">'
            . '<figcaption id="caption-attachment-3" class="wp-caption-text">A caption</figcaption>'
            . '</figure><p>Text</p>'
        ));

        $this->assertSame(0, $xpath->query('//figure//figure')->length, 'Figures must not be nested');

        $figures = $xpath->query('//figure');
        $this->assertSame(1, $figures->length);
        $this->assertStringContainsString('u-float--right', $figures->item(0)->getAttribute('class'));
        $this->assertSame('A caption', trim($xpath->query('//figure/figcaption')->item(0)->textContent));
        $this->assertSame(1, $xpath->query('//p[text()="Text"]')->length);
    }

    #[TestDox('normalizeImages() keeps the link when unwrapping a caption figure around a linked image')]
    public function testCaptionFigureWithLinkIsUnwrappedAndKeepsLink(): void
    {
        $xpath = $this->loadBody($this->normalize(
            '<figure class="wp-caption alignleft">'
            . '<a href="https://example.com/a-large.png"><img class="wp-image-4" src="https://example.com/a.png" alt="A" width="300"></a>'
            . '<figcaption class="wp-caption-text">Linked</figcaption>'
            . '</figure>'
        ));

        $this->assertSame(0, $xpath->query('//figure//figure')->length, 'Figures must not be nested');

        $figure = $xpath->query('//a/figure')->item(0);
        $this->assertNotNull($figure, 'The link must wrap the rendered component');
        $this->assertStringContainsString('u-float--left', $figure->getAttribute('class'));
    }

    #[TestDox('normalizeImages() does not unwrap figures that contain more than the image')]
    public function testFigureWithOtherContentIsKept(): void
    {
        $xpath = $this->loadBody($this->normalize(
            '<figure class="gallery"><img class="wp-image-5" src="https://example.com/a.png" alt="A" width="300"><p>Other</p></figure>'
        ));

        $outer = $xpath->query('//div[@id="root"]/figure')->item(0);
        $this->assertNotNull($outer);
        $this->assertSame('gallery', $outer->getAttribute('class'));
    }
}
