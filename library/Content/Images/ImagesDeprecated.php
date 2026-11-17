<?php

namespace Municipio\Content\Images;

use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;
use Municipio\Integrations\Component\ImageResolver;

class ImagesDeprecated implements ImagesInterface
{
    public function __construct()
    {
        add_action('wp', function () {
            add_filter('the_content', [$this, 'normalizeImages'], has_blocks(get_post_field('post_content', get_the_ID())) ? 5 : 11);
        });

        add_filter('Municipio/Content/ImageNormalized', [$this, 'imageHasBeenNormalized'], 10, 2);
    }

    public function normalizeImages($content)
    {
        $encoding = '<?xml encoding="utf-8" ?>';

        if (!has_blocks($content) && str_contains($content, '<img')) {
            $dom = new \DOMDocument();
            $dom->loadHTML($encoding . $content, LIBXML_NOERROR);
            $xpath = new \DOMXPath($dom);

            $links = $dom->getElementsByTagName('a');
            $images = $xpath->query('//img[contains(@class, "wp-image-")]');

            $this->processLinks($dom, $links);
            $this->processImages($dom, $images);

            $content = $dom->saveHTML();

            return str_replace(
                [$encoding, '<html>', '</html>', '<body>', '</body>'],
                '',
                \Municipio\Helper\Post::replaceBuiltinClasses($content),
            );
        }

        return $content;
    }

    private function processLinks($dom, $links)
    {
        if (!is_object($links) || empty($links))
            return;

        foreach ($links as $link) {
            if (!isset($link->firstChild) || $link->firstChild->nodeName !== 'img')
                continue;

            $linkedImage = $link->firstChild;
            if ($this->isSelfLinked($link, $linkedImage)) {
                $linkedImage->setAttribute('parsed', '1');
                $captionText = $this->extractCaption($link->parentNode);
                $altText = $linkedImage->getAttribute('alt') ?: $captionText;
                $this->replaceWithBladeTemplate($dom, $link, $linkedImage, $altText, $captionText);
            }
        }
    }

    private function processImages($dom, $images)
    {
        if (!is_object($images) || empty($images))
            return;

        foreach ($images as $image) {
            if (apply_filters('Municipio/Content/ImageNormalized', false, $image))
                continue;

            $captionText = $this->extractCaption($image->parentNode);
            $altText = $image->getAttribute('alt') ?: $captionText;

            $this->replaceWithBladeTemplate($dom, $image, $image, $altText, $captionText);
        }
    }

    private function isSelfLinked($link, $image)
    {
        $imgDir = pathinfo($image->getAttribute('src'), PATHINFO_DIRNAME);
        $linkDir = pathinfo($link->getAttribute('href'), PATHINFO_DIRNAME);
        return $linkDir === $imgDir;
    }

    public function extractCaption($parentNode)
    {
        $captionText = '';

        if ($parentNode instanceof \DOMElement && $parentNode->getElementsByTagName('figcaption')->length > 0) {
            foreach ($parentNode->getElementsByTagName('figcaption') as $caption) {
                $captionText = wp_strip_all_tags($caption->textContent);

                if ($caption->parentNode instanceof \DOMNode) {
                    $caption->parentNode->removeChild($caption);
                }
            }
        }

        return $captionText;
    }

    private function replaceWithBladeTemplate($dom, $element, $image, $altText, $captionText)
    {
        $url = $this->sanitizeRequestUrl($image->getAttribute('src'));
        $attachmentId = attachment_url_to_postid($url);

        $captionFigure = $this->getCaptionFigure($image);
        $wrapper       = $captionFigure ?? $image->parentNode;

        $classes = ImageAlignment::resolveClassList(
            $wrapper instanceof \DOMElement
                ? ImageAlignment::splitClassAttribute($wrapper->getAttribute('class'))
                : [],
            ImageAlignment::splitClassAttribute($image->getAttribute('class'))
        );

        if (is_numeric($attachmentId) && !empty($attachmentId)) {
            $contentContainerWidth = $this->getPageWidth();
            $imageSrc = wp_get_attachment_image_src($attachmentId, [$contentContainerWidth, false]);

            if ($imageSrc && isset($imageSrc[0])) {
                $html = render_blade_view('partials.content.image', [
                    'src' => ImageComponentContract::factory(
                        (int) $attachmentId,
                        [$contentContainerWidth, false],
                        new ImageResolver()
                    ),
                    'alt' => $altText,
                    'caption' => $captionText,
                    'classList' => $classes,
                    'imgAttributeList' => ['parsed' => true],
                    'attributeList' => [
                        'style' => sprintf(
                            'width: min(%s, 100%%); height: auto;',
                            ($image->getAttribute('width') ?? 1920) . 'px',
                        ),
                    ],
                ]);
            }
        }

        if (empty($attachmentId)) {
            $html = render_blade_view('partials.content.image', [
                'src' => $url,
                'alt' => $altText,
                'caption' => $captionText,
                'classList' => $classes,
                'imgAttributeList' => ['parsed' => true],
                'attributeList' => [
                    'style' => sprintf(
                        'width: min(%s, 100%%); height: auto;',
                        ($image->getAttribute('width') ?? 1920) . 'px',
                    ),
                ],
            ]);
        }

        if (!empty($html)) {
            $newNode = \Municipio\Helper\FormatObject::createNodeFromString($dom, $html);
            if ($image)
                $image->replaceWith($newNode);

            if ($captionFigure !== null) {
                $this->unwrap($captionFigure);
            }
        }
    }

    /**
     * Get the caption figure (classic editor [caption] shortcode) that wraps an image,
     * if the figure contains nothing but the image, optionally wrapped in a link,
     * and its figcaption.
     *
     * The rendered image component is itself a <figure> with caption and alignment
     * classes. Keeping the caption figure around it would nest two floating figures,
     * which collapses the image to zero width, so the caption figure is unwrapped.
     *
     * @param \DOMElement $image
     * @return \DOMElement|null
     */
    private function getCaptionFigure(\DOMElement $image): ?\DOMElement
    {
        $inner  = $image;
        $figure = $image->parentNode;

        if ($figure instanceof \DOMElement && strtolower($figure->nodeName) === 'a') {
            $inner  = $figure;
            $figure = $figure->parentNode;
        }

        if (
            !$figure instanceof \DOMElement
            || strtolower($figure->nodeName) !== 'figure'
            || !$figure->parentNode instanceof \DOMNode
        ) {
            return null;
        }

        foreach ($figure->childNodes as $child) {
            if ($child === $inner) {
                continue;
            }

            if ($child instanceof \DOMElement && strtolower($child->nodeName) !== 'figcaption') {
                return null;
            }

            if ($child instanceof \DOMText && trim($child->textContent) !== '') {
                return null;
            }
        }

        return $figure;
    }

    /**
     * Replace an element with its children.
     *
     * @param \DOMElement $element
     * @return void
     */
    private function unwrap(\DOMElement $element): void
    {
        while ($element->firstChild) {
            $element->parentNode->insertBefore($element->firstChild, $element);
        }

        $element->parentNode->removeChild($element);
    }

    public function imageHasBeenNormalized($normalized, $image): bool
    {
        return $image->getAttribute('parsed') ? true : $normalized;
    }

    private function sanitizeRequestUrl($url): string
    {
        $parsedUrl = parse_url($url);
        if (!$parsedUrl)
            return $url;

        $sanitizedUrl = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $sanitizedUrl .= isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $sanitizedUrl .= isset($parsedUrl['port']) && !in_array($parsedUrl['port'], [80, 443]) ? ':' . $parsedUrl['port'] : '';
        $sanitizedUrl .= isset($parsedUrl['path']) ? $parsedUrl['path'] : '';

        return preg_replace('/-(\d+x\d+)(?=\.\w{3,4}$)/', '', $sanitizedUrl);
    }

    private function getPageWidth(): int
    {
        return (int) ceil(get_theme_mod('container_content', 900) / 100) * 100;
    }
}
