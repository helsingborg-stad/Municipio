<?php

namespace Municipio\Toc\Utils;

use DOMDocument;
use DOMXPath;
use DOMElement;
use WpService\WpService;

class TableOfContents
{
    private const ANCHOR_PREFIX = 'toc-';
    private DOMDocument $domObject;
    private array $headings = [];

    /**
     * TableOfContents constructor.
     *
     * @param string $html The HTML content to parse for headings.
     */
    public function __construct(private string $html, private WpService $wpService)
    {
        $this->domObject = self::createDomFromHtml($html);
        $this->headings  = self::extractHeadingsFromHtml($this->domObject, $wpService);
    }

    /**
     * Returns a table of contents array based on the headings in the provided HTML.
     *
     * This method extracts headings (h1–h6) from the HTML and builds a nested
     * table of contents structure, allowing for easy navigation within the document.
     *
     * @return array The structured table of contents.
     */
    public function getTableOfContents(): array
    {
        return self::buildNestedToc($this->headings) ?? [];
    }

    /**
     * Adds anchor IDs to headings in the provided HTML string.
     *
     * This method processes the HTML to find headings (h1–h6) and injects
     * unique IDs based on their text content, allowing for easy linking.
     *
     * @return string The modified HTML with anchor IDs added to headings.
     */
    public function getDocumentWithAnchors(): string
    {
        return self::injectSlugsIntoHtml(
            $this->html,
            $this->headings,
            $this->domObject
        );
    }

    /**
     * Creates and returns a DOMDocument from the provided HTML string.
     *
     * @param string $html
     * @return DOMDocument
     */
    private static function createDomFromHtml(string $html): DOMDocument
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();
        return $dom;
    }

    /**
     * Extracts headings (h1–h6) from the HTML and returns them as an array.
     *
     * @param DOMDocument $dom
     * @return array
     */
    private static function extractHeadingsFromHtml(DOMDocument $dom, WpService $wpService): array
    {
        $xpath    = new DOMXPath($dom);
        $elements = $xpath->query('//h2 | //h3 | //h4');

        $headings = [];
        foreach ($elements as $el) {
            if (!$el instanceof DOMElement) {
                continue;
            }

            $text  = trim($el->textContent);
            $level = (int) substr($el->nodeName, 1);
            $slug  = self::generateSlug($text, $wpService);

            $headings[] = compact('text', 'level', 'slug');
        }

        return $headings;
    }

    /**
     * Injects slug-based IDs into headings within the HTML.
     *
     * @param string $html
     * @param array $headings
     * @param DOMDocument $dom
     * @return string
     */
    private static function injectSlugsIntoHtml(string $html, array $headings, DOMDocument $dom): string
    {
        if (empty($html) || empty($headings)) {
            return $html;
        }

        $xpath    = new DOMXPath($dom);
        $elements = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');

        foreach ($elements as $i => $el) {
            if (isset($headings[$i]) && $el instanceof DOMElement) {
                $el->setAttribute('id', $headings[$i]['slug']);
                $el->setAttribute('data-update-hash-when-focused', '1');
                $el->setAttribute('data-update-hash-value', $headings[$i]['slug']);
            }
        }

        return $dom->saveHTML();
    }

    /**
     * Builds a nested table of contents array based on heading levels.
     *
     * @param array $headings
     * @param int $startLevel
     * @param int $maxDepth
     * @return array
     */
    private static function buildNestedToc(array $headings, int $startLevel = 2, int $maxDepth = 3): array
    {
        $items = array_filter($headings, fn($h) => $h['level'] >= $startLevel && $h['level'] < $startLevel + $maxDepth);

        $toc = $stack = [];
        foreach ($items as $item) {
            $tocItem = [
                'icon'          => [
                    'icon'   => 'arrow_forward',
                    'size'   => 'sm',
                    'filled' => true,
                    'color'  => 'primary'
                ],
                'label'         => $item['text'],
                'level'         => $item['level'],
                'href'          => '#' . $item['slug'],
                'children'      => [],
                'attributeList' => [
                    'data-highlight-on-hash-match'       => $item['slug'],
                    'data-highlight-on-hash-match-class' => 'is-current',
                ]
            ];

            while (!empty($stack) && $tocItem['level'] <= end($stack)['level']) {
                array_pop($stack);
            }

            if (empty($stack)) {
                $toc[]   = $tocItem;
                $stack[] = &$toc[array_key_last($toc)];
            } else {
                $parent               = &$stack[array_key_last($stack)];
                $parent['children'][] = $tocItem;
                $stack[]              = &$parent['children'][array_key_last($parent['children'])];
            }
        }

        return $toc;
    }

    /**
     * Generates a URL-safe slug from the given text.
     *
     * @param string $text
     * @return string
     */
    private static function generateSlug(string $text, WpService $wpService): string
    {
        return self::ANCHOR_PREFIX . $wpService->sanitizeTitle($text);
    }
}
