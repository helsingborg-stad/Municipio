<?php

namespace Municipio\Content\Images;

class ImagesDeprecated implements ImagesInterface
{
    public function __construct()
    {
        add_filter('the_content', [$this, 'normalizeImages'], 11);
        add_filter('Municipio/Content/ImageNormalized', [$this, 'imageHasBeenNormalized'], 10, 2);
    }

    public function normalizeImages($content)
    {
        $encoding = '<?xml encoding="utf-8" ?>';

        if (!has_blocks($content) && str_contains($content, '<img')) {
            $dom = new \DOMDocument();
            $dom->loadHTML($encoding . $content, LIBXML_NOERROR);
            $xpath = new \DOMXPath($dom);

            $links  = $dom->getElementsByTagName('a');
            $images = $xpath->query('//img[contains(@class, "wp-image-")]');

            $this->processLinks($dom, $links);
            $this->processImages($dom, $images);

            $content = $dom->saveHTML();

            return str_replace(
                [$encoding, '<html>', '</html>', '<body>', '</body>'],
                '',
                \Municipio\Helper\Post::replaceBuiltinClasses($content)
            );
        }

        return $content;
    }

    private function processLinks($dom, $links)
    {
        if (!is_object($links) || empty($links)) return;

        foreach ($links as $link) {
            if (!isset($link->firstChild) || $link->firstChild->nodeName !== 'img') continue;

            $linkedImage = $link->firstChild;
            if ($this->isSelfLinked($link, $linkedImage)) {
                $linkedImage->setAttribute('parsed', '1');
                $captionText = $this->extractCaption($link->parentNode);
                $altText     = $linkedImage->getAttribute('alt') ?: $captionText;
                $this->replaceWithBladeTemplate($dom, $link, $linkedImage, $altText, $captionText);
            }
        }
    }

    private function processImages($dom, $images)
    {
        if (!is_object($images) || empty($images)) return;

        foreach ($images as $image) {
            if (apply_filters('Municipio/Content/ImageNormalized', false, $image)) continue;

            $captionText = $this->extractCaption($image->parentNode);
            $altText     = $image->getAttribute('alt') ?: $captionText;

            $this->replaceWithBladeTemplate($dom, $image, $image, $altText, $captionText);
        }
    }

    private function isSelfLinked($link, $image)
    {
        $imgDir  = pathinfo($image->getAttribute('src'), PATHINFO_DIRNAME);
        $linkDir = pathinfo($link->getAttribute('href'), PATHINFO_DIRNAME);
        return $linkDir === $imgDir;
    }

    private function extractCaption($parentNode)
    {
        $captionText = '';

        if ($parentNode instanceof \DOMElement && $parentNode->getElementsByTagName('figcaption')->length > 0) {
            foreach ($parentNode->getElementsByTagName('figcaption') as $caption) {
                $captionText = wp_strip_all_tags($caption->textContent);
                $parentNode->removeChild($caption);
            }
        }

        return $captionText;
    }

    private function replaceWithBladeTemplate($dom, $element, $image, $altText, $captionText)
    {
        $url          = $this->sanitizeRequestUrl($image->getAttribute('src'));
        $attachmentId = attachment_url_to_postid($url);

        $classes = $image->parentNode instanceof \DOMElement ? explode(' ', $image->parentNode->getAttribute('class') ?? []) : [];

        if (is_numeric($attachmentId) && !empty($attachmentId)) {
            $contentContainerWidth = $this->getPageWidth();
            $imageSrc = wp_get_attachment_image_src($attachmentId, [$contentContainerWidth, false]);

            if ($imageSrc && isset($imageSrc[0])) {
                $html = render_blade_view('partials.content.image', [
                    'src'              => $imageSrc[0],
                    'alt'              => $altText,
                    'caption'          => $captionText,
                    'classList'        => $classes,
                    'imgAttributeList' => ['parsed' => true],
                    'attributeList'    => [
                        'style' => sprintf(
                            'width: min(%s, 100%%); height: auto;',
                            ($image->getAttribute('width') ?? 1920) . 'px'
                        )
                    ]
                ]);
            }
        }

        if (empty($attachmentId)) {
            $html = render_blade_view('partials.content.image', [
                'src'              => $url,
                'alt'              => $altText,
                'caption'          => $captionText,
                'classList'        => $classes,
                'imgAttributeList' => ['parsed' => true],
                'attributeList'    => [
                    'style' => sprintf(
                        'width: min(%s, 100%%); height: auto;',
                        ($image->getAttribute('width') ?? 1920) . 'px'
                    )
                ]
            ]);
        }

        if (!empty($html)) {
            $newNode = \Municipio\Helper\FormatObject::createNodeFromString($dom, $html);
            if ($image) $image->replaceWith($newNode);
        }
    }

    public function imageHasBeenNormalized($normalized, $image): bool
    {
        return $image->getAttribute('parsed') ? true : $normalized;
    }

    private function sanitizeRequestUrl($url): string
    {
        $parsedUrl = parse_url($url);
        if (!$parsedUrl) return $url;

        $sanitizedUrl  = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
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
