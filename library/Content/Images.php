<?php

namespace Municipio\Content;

use Municipio\Integrations\Component\ImageResolver;
use ComponentLibrary\Integrations\Image\Image as ImageComponentContract;

class Images
{
    public function __construct()
    {
        add_filter('the_content', array($this, 'normalizeImages'), 11);
        add_filter('Municipio/Content/ImageNormalized', array($this, 'imageHasBeenNormalized'), 10, 2);
    }

    /**
     * Normalize images
     *
     * @param string $content
     *
     * @return string
     */
    public static function normalizeImages($content)
    {
        $encoding = '<?xml encoding="utf-8" ?>';

        if ('one-page.blade.php' !== get_page_template_slug() && !has_blocks($content) && str_contains($content, '<img')) {
            $dom = new \DOMDocument();
            $dom->loadHTML($encoding . $content, LIBXML_NOERROR);

            $xpath = new \DOMXPath($dom);

            $links  = $dom->getElementsByTagName('a');
            $images = $xpath->query('//img[contains(@class, "wp-image-")]');

            self::processLinks($dom, $links);
            self::processImages($dom, $images);

            $content = $dom->saveHTML();

            return str_replace([$encoding, '<html>', '</html>', '<body>', '</body>'], '', \Municipio\Helper\Post::replaceBuiltinClasses($content));
        }

        return $content;
    }

    /**
     * Process links
     *
     * @param \DOMDocument $dom
     * @param \DOMNodeList $links
     *
     * @return void
     */
    private static function processLinks($dom, $links)
    {
        if (!is_object($links) || empty($links)) {
            return;
        }

        foreach ($links as $link) {
            if (!isset($link->firstChild) || $link->firstChild->nodeName !== 'img') {
                continue;
            }
            $linkedImage = $link->firstChild;
            if (self::isSelfLinked($link, $linkedImage)) {
                $captionText = self::extractCaption($link->parentNode);
                $altText     = $linkedImage->getAttribute('alt') ?: $captionText;
                self::replaceWithBladeTemplate($dom, $link, $linkedImage, $altText, $captionText);
            }
        }
    }

    /**
     * Process images
     *
     * @param \DOMDocument $dom
     * @param \DOMNodeList $images
     *
     * @return void
     */
    private static function processImages($dom, $images)
    {
        //Check that we actually have images
        if (!is_object($images) || empty($images)) {
            return;
        }

        foreach ($images as $image) {
            //Only process images that are not already normalized
            if (apply_filters('Municipio/Content/ImageNormalized', false, $image)) {
                continue;
            }

            //Get caption and alt text
            $captionText = self::extractCaption($image->parentNode);
            $altText     = $image->getAttribute('alt') ?: $captionText;

            //Replace image with blade template
            self::replaceWithBladeTemplate($dom, $image, $image, $altText, $captionText);
        }
    }

    /**
     * Check if the link is self linked
     *
     * @param \DOMElement $link
     *
     * @return bool
     */
    private static function isSelfLinked($link, $image)
    {
        $imgDir  = pathinfo($image->getAttribute('src'), PATHINFO_DIRNAME);
        $linkDir = pathinfo($link->getAttribute('href'), PATHINFO_DIRNAME);
        return $linkDir === $imgDir;
    }

    /**
     * Extract the caption from the parent node
     *
     * @param \DOMElement $parentNode
     *
     * @return string     The caption text
     */
    private static function extractCaption($parentNode)
    {
        // Initialize an empty string for the caption text
        $captionText = '';

        // Ensure the parent node is not null and is an instance of \DOMElement
        if ($parentNode instanceof \DOMElement && $parentNode->getElementsByTagName('figcaption')->length > 0) {
            foreach ($parentNode->getElementsByTagName('figcaption') as $caption) {
                // Extract and clean the caption text
                $captionText = wp_strip_all_tags($caption->textContent);
                // Remove the caption node from the parent
                $parentNode->removeChild($caption);
            }
        }

        // Return the extracted caption text (empty string if none found)
        return $captionText;
    }

    /**
     * Replace the image with a blade template
     *
     * @param \DOMDocument $dom
     * @param \DOMElement $element
     * @param \DOMElement $image
     * @param string $altText
     * @param string $captionText
     *
     * @return void
     */
    private static function replaceWithBladeTemplate($dom, $element, $image, $altText, $captionText)
    {
        $url          = self::sanitizeRequestUrl($image->getAttribute('src'));
        $attachmentId = attachment_url_to_postid($url);

        //Handle as know local image (in wp database)
        if (is_numeric($attachmentId) && !empty($attachmentId)) {
            //Width
            $conentContainerWidth = self::getPageWidth();

            //Get image contract
            /*$imageComponentContract = ImageComponentContract::factory(
                (int) $attachmentId,
                [$conentContainerWidth, false],
                new ImageResolver()
            );*/

            $imageSrc = wp_get_attachment_image_src($attachmentId, [$conentContainerWidth, false]);

            if ($imageSrc && isset($imageSrc[0])) {
                $html = render_blade_view('partials.content.image', [
                    'src'              => $imageSrc[0],
                    'caption'          => $captionText,
                    'classList'        => explode(' ', $image->getAttribute('class') ?? []),
                    'imgAttributeList' => [
                        'parsed' => true,
                    ],
                    'attributeList'    => [
                        'style' => sprintf(
                            'width: min(%s, 100%%); height: auto;',
                            ($image->getAttribute('width') ?? 1920) . 'px'
                        )
                    ]
                ]);
            }
        }

        //Handle as unknown or external image (not in wp database)
        if (empty($attachmentId)) {
            $html = render_blade_view('partials.content.image', [
                'src'              => $url,
                'alt'              => $altText,
                'caption'          => $captionText,
                'classList'        => explode(' ', $image->getAttribute('class') ?? []),
                'imgAttributeList' => [
                    'parsed' => true
                ],
                'attributeList'    => [
                    'style' => sprintf(
                        'width: min(%s, 100%%); height: auto;',
                        ($image->getAttribute('width') ?? 1920) . 'px'
                    )
                ]
            ]);
        }

        if (is_string($html) && !empty($html)) {
            $newNode = \Municipio\Helper\FormatObject::createNodeFromString($dom, $html);
            if ($newNode instanceof \DOMElement && $element->parentNode instanceof \DOMElement) {
                $element->parentNode->replaceChild($newNode, $element);
            }
        }
    }

    /**
     * Check if image has been normalized
     *
     * @param bool $normalized
     *
     * @return bool
     */
    public static function imageHasBeenNormalized($normalized, $image): bool
    {
        if ($image->getAttribute('parsed')) {
            return true;
        }
        return $normalized;
    }

    /**
     * Sanitize the request URL
     *
     * @param string $url
     *
     * @return string
     */
    private static function sanitizeRequestUrl($url): string
    {
        $parsedUrl = parse_url($url);

        if (!$parsedUrl) {
            return $url;
        }

        // Reconstruct the URL without the query part
        $sanitizedUrl  = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $sanitizedUrl .= isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $sanitizedUrl .= isset($parsedUrl['port']) && !in_array($parsedUrl['port'], [80, 443]) ? ':' . $parsedUrl['port'] : '';
        $sanitizedUrl .= isset($parsedUrl['path']) ? $parsedUrl['path'] : '';

        // Remove the pixelsize
        $sanitizedUrl = preg_replace('/-(\d+x\d+)(?=\.\w{3,4}$)/', '', $sanitizedUrl);

        return $sanitizedUrl;
    }

    /**
     * Get the page width, rounded to nearest 100
     *
     * @return int
     */
    private static function getPageWidth(): int
    {
        return (int) ceil(get_theme_mod('container_content', 900) / 100) * 100;
    }
}
