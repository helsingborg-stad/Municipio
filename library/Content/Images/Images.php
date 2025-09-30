<?php

namespace Municipio\Content\Images;

/**
 * Class Images
 */
class Images implements ImagesInterface
{
    /**
     * Images constructor.
     */
    public function __construct()
    {
        add_filter('the_content', [$this, 'normalizeImages'], 5);
        add_filter('Municipio/Content/ImageNormalized', [$this, 'imageHasBeenNormalized'], 10, 2);
    }

    /**
     * Normalize images
     *
     * @param string $content
     *
     * @return string
     */
    public function normalizeImages($content)
    {
        if (has_blocks($content) || !str_contains($content, '<img')) {
            return $content;
        }

         $htmlDom = \DOM\HTMLDocument::createFromString(
            '<!DOCTYPE html><html><body>' . $content . '</body></html>',
            0,
            'UTF-8'
        );

        $this->processImages($htmlDom->querySelectorAll('figure'));
        $content = $htmlDom->saveHTML();

        return str_replace(
            ['<html>', '</html>', '<body>', '</body>'],
            '',
            \Municipio\Helper\Post::replaceBuiltinClasses($content)
        );
    }

    /**
     * Process images
     */
    private function processImages($figures): void
    {
        if (!is_object($figures) || empty($figures)) {
            return;
        }

        foreach ($figures as $figure) {
            if (apply_filters('Municipio/Content/ImageNormalized', false, $figure)) {
                continue;
            }

            $image       = $figure->querySelector('img');
            
            if (!$image) {
                continue;
            }

            $this->replaceWithBladeTemplate($image, $figure);
        }
    }

    /**
     * Extract the caption from the parent node
     * 
     * @param \Dom\HTMLElement $parentNode The parent node of the image
     * @return string The extracted caption text
     */
    private function extractCaption($figure): string
    {
        $captionText = '';
        $caption = $figure->querySelector('figcaption');

        if ($caption) {
            $captionText = wp_strip_all_tags($caption->textContent);
            $figure->removeChild($caption);
        }

        return $captionText;
    }

    private function extractLink($figure): ?string
    {
        $link = $figure->querySelector('a');

        if ($link) {
            return $link->getAttribute('href');
        }

        return null;
    }

    /**
     * Replace the image with a blade template
     *
     * @param \Dom\HTMLElement $image The image element
     * @param string $altText The alt text for the image
     * @param string $captionText The caption text for the image
     */
    private function replaceWithBladeTemplate($image, $figure): void
    {
        $url          = $this->sanitizeRequestUrl($image->getAttribute('src'));
        $captionText  = $this->extractCaption($figure);
        $link         = $this->extractLink($figure);
        $altText      = $image->getAttribute('alt') ?: $captionText;
        $attachmentId = attachment_url_to_postid($url);

        $classes = explode(' ', $figure->getAttribute('class')) ?: [];

        $html = $this->renderBladeImage($image, $attachmentId, $url, $altText, $captionText, $classes);

        if (is_numeric($attachmentId) && !empty($attachmentId)) {
            $conentContainerWidth = $this->getPageWidth();
            $imageSrc = wp_get_attachment_image_src($attachmentId, [$conentContainerWidth, false]);

            if ($imageSrc && isset($imageSrc[0])) {
                $html = $this->renderBladeImage($image, $attachmentId, $imageSrc[0], $altText, $captionText, $classes);
            }
        }

        if (empty($attachmentId)) {
            $html = $this->renderBladeImage($image, $attachmentId, $url, $altText, $captionText, $classes);
        }

        if (!is_string($html) || empty($html)) {
            return;
        }

        $doc = $figure->ownerDocument;
        $wrapper = $this->createAlignmentDiv($doc, $figure);
        $figure->replaceWith($wrapper);
        $content = $this->convertHtmlStringToDomFragment($doc, $wrapper, $html);

        if ($link) {
            $linkElement = $doc->createElement('a');
            $linkElement->setAttribute('href', esc_url($link));
            $linkElement->appendChild($content);
            $content = $linkElement;
        }

        if ($wrapper->firstChild) {
            $wrapper->insertBefore($content, $wrapper->firstChild);
        } else {
            $wrapper->appendChild($content);
        }
        // $wrapper->appendChild($content);
        // $this->replaceNodeWithHTML5($image, $html);
    }

    /**
     * Create alignment classes on figure element based on image classes
     * 
     * @param \Dom\HTMLElement $figure The figure element to add alignment classes to
     */
    private function createAlignmentDiv(\Dom\Document $doc, \Dom\HTMLElement $figure): \Dom\HTMLElement
    {
        $wrapper = $doc->createElement('div');
        $wrapper->setAttribute('class', 'municipio-image-wrapper');

        if (!strpos($figure->getAttribute('class'), 'alignnone') !== false) {
            if ($figure->nextElementSibling && $figure->nextElementSibling->tagName === 'P') {
                $pTag = $figure->nextElementSibling;
                $pTag->remove();
                $wrapper->appendChild($pTag);
            }
        }

        return $wrapper;
    }

    /**
     * Render the blade image template
     * 
     * @param \Dom\HTMLElement $image The image element
     * @param int|string|null $attachmentId The attachment ID of the image
     * @param string $url The URL of the image
     * @param string $altText The alt text for the image
     * @param string $captionText The caption text for the image
     * @param array $classes The classes to add to the image wrapper
     * @return string The rendered HTML from the blade template
     */
    private function renderBladeImage($image, $attachmentId, $url, $altText, $captionText, $classes)
    {
        $html = '';

        $html = render_blade_view('partials.content.image', [
            'src'              => $url,
            'alt'              => $altText,
            'caption'          => $captionText,
            'classList'        => $classes,
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

        return $html;
    }

    /**
     * Replace a node with HTML5 content
     * 
     * @param \Dom\HTMLElement $node The node to replace
     * @param string $html The HTML content to insert
     */
    private function convertHtmlStringToDomFragment(\Dom\Document $doc, \Dom\HTMLElement $node, string $html): \Dom\DocumentFragment|\Dom\HTMLElement
    {
        if (empty($html)) {
            return $node;
        }

        $doc = $node->ownerDocument;

        // Parse the HTML fragment in a temporary HTML5 document
        $tmpDoc = \Dom\HTMLDocument::createFromString(
            "<!DOCTYPE html><html><body>$html</body></html>"
        );

        // Create a document fragment in the main document
        $fragment = $doc->createDocumentFragment();

        // Import all child nodes from tmpDoc's body into the fragment
        foreach ($tmpDoc->body->childNodes as $child) {
            $fragment->appendChild($doc->importNode($child, true));
        }

        return $fragment;
    }

    /**
     * Check if image has been normalized
     */
    public function imageHasBeenNormalized($normalized, $image): bool
    {
        if ($image->getAttribute('parsed')) {
            return true;
        }

        return $normalized;
    }

    /**
     * Sanitize the request URL
     */
    private function sanitizeRequestUrl($url): string
    {
        $parsedUrl = parse_url($url);

        if (!$parsedUrl) {
            return $url;
        }

        $sanitizedUrl  = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $sanitizedUrl .= isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $sanitizedUrl .= isset($parsedUrl['port']) && !in_array($parsedUrl['port'], [80, 443]) ? ':' . $parsedUrl['port'] : '';
        $sanitizedUrl .= isset($parsedUrl['path']) ? $parsedUrl['path'] : '';

        $sanitizedUrl = preg_replace('/-(\d+x\d+)(?=\.\w{3,4}$)/', '', $sanitizedUrl);

        return $sanitizedUrl;
    }

    /**
     * Get the page width, rounded to nearest 100
     */
    private function getPageWidth(): int
    {
        return (int) ceil(get_theme_mod('container_content', 900) / 100) * 100;
    }

    /**
     * Singleton accessor
     */
    public static function GetImageNormalizer(): ImagesInterface
    {
        static $imageNormalizer = null;

        if ($imageNormalizer !== null) {
            return $imageNormalizer;
        }

        if (class_exists('\DOM\HTMLDocument')) {
            $imageNormalizer = new \Municipio\Content\Images\Images();
        } else {
            $imageNormalizer = new \Municipio\Content\Images\ImagesDeprecated();
        }

        return $imageNormalizer;
    }
}