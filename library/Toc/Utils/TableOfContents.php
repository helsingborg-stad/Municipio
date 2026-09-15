<?php

namespace Municipio\Toc\Utils;

use DOMDocument;
use DOMElement;
use DOMXPath;
use WpService\WpService;

/**
 * Class TableOfContents
 *
 * This class is responsible for generating a table of contents from HTML content.
 * It extracts headings (h1–h6), generates unique slugs for them, and builds a nested
 * structure that can be used for navigation.
 */
class TableOfContents
{
    private const ANCHOR_PREFIX = 'toc-';
    private const START_LEVEL = 2;
    private const DEFAULT_NUMBER_OF_LEVELS = 3;

    private DOMDocument $domObject;
    private array $headings = [];
    private string $headingSelector;

    /**
     * TableOfContents constructor.
     *
     * @param string $html The HTML content to parse for headings.
     * @param WpService $wpService The WordPress service instance.
     * @param int $numberOfLevels How many heading levels to include, starting from h2 (e.g. 3 = h2, h3, h4).
     */
    public function __construct(
        private string $html,
        private WpService $wpService,
        private int $numberOfLevels = self::DEFAULT_NUMBER_OF_LEVELS,
    ) {
        $this->headingSelector = self::buildHeadingSelector($this->numberOfLevels);
        $this->domObject = self::createDomFromHtml($html);
        $this->headings = self::extractHeadingsFromHtml($this->domObject, $wpService, $this->headingSelector);
    }

    /**
     * Returns a table of contents array based on the headings in the provided HTML.
     *
     * This method extracts headings from the HTML and builds a nested
     * table of contents structure, allowing for easy navigation within the document.
     *
     * @return array The structured table of contents.
     */
    public function getTableOfContents(): array
    {
        return self::buildNestedToc($this->headings, self::START_LEVEL, $this->numberOfLevels) ?? [];
    }

    /**
     * Builds an XPath selector matching direct body-level headings for the configured levels.
     *
     * @param int $numberOfLevels How many heading levels to include, starting from h2.
     * @return string
     */
    private static function buildHeadingSelector(int $numberOfLevels): string
    {
        $numberOfLevels = max(1, min($numberOfLevels, 6 - self::START_LEVEL + 1));

        $selectors = [];
        for ($level = self::START_LEVEL; $level < (self::START_LEVEL + $numberOfLevels); $level++) {
            $selectors[] = "/html/body/h{$level}";
        }

        return implode(' | ', $selectors);
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
            $this->domObject,
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
     * Extracts headings from the HTML and returns them as an array.
     *
     * @param DOMDocument $dom
     * @param WpService $wpService
     * @param string $headingSelector
     * @return array
     */
    private static function extractHeadingsFromHtml(DOMDocument $dom, WpService $wpService, string $headingSelector): array
    {
        $xpath = new DOMXPath($dom);
        $elements = $xpath->query($headingSelector);

        $headings = [];
        foreach ($elements as $el) {
            $text = trim($el->textContent);
            $level = (int) substr($el->nodeName, 1);
            $slug = self::generateSlug($text, $wpService);

            if (empty($text) || empty($slug)) {
                continue;
            }

            // Keep a reference to the matched element so injection targets this exact
            // heading instead of re-querying and matching by array index.
            $headings[] = compact('text', 'level', 'slug', 'el');
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

        foreach ($headings as $heading) {
            $el = $heading['el'];

            if ($el instanceof DOMElement) {
                $el->setAttribute('id', $heading['slug']);
                $el->setAttribute('data-update-hash-when-focused', '1');
                $el->setAttribute('data-update-hash-value', $heading['slug']);
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
        $items = array_filter($headings, fn($h) => $h['level'] >= $startLevel && $h['level'] < ($startLevel + $maxDepth));

        $toc = $stack = [];
        foreach ($items as $item) {
            $tocItem = [
                'label' => $item['text'],
                'level' => $item['level'],
                'href' => '#' . $item['slug'],
                'children' => [],
                'attributeList' => [
                    'data-highlight-on-hash-match' => $item['slug'],
                    'data-highlight-on-hash-match-class' => 'is-current',
                ],
            ];

            while (!empty($stack) && $tocItem['level'] <= end($stack)['level']) {
                array_pop($stack);
            }

            if (empty($stack)) {
                $toc[] = $tocItem;
                $stack[] = &$toc[array_key_last($toc)];
            } else {
                $parent = &$stack[array_key_last($stack)];
                $parent['children'][] = $tocItem;
                $stack[] = &$parent['children'][array_key_last($parent['children'])];
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
