<?php

namespace Municipio\Content;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

class HeadingStructure implements Hookable
{
    public function __construct(
        private AddFilter $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter(
            'Website/HTML/output',
            [$this, 'correctHeadingStructure'],
            10,
            1,
        );
    }

    public function correctHeadingStructure(string $html): string
    {
        $htmlDom = @\DOM\HTMLDocument::createFromString($html, 0, 'UTF-8');
        $headings = $htmlDom->querySelectorAll('h1, h2, h3, h4, h5, h6');

        $headingElements = iterator_to_array($headings);

        $context = null;
        $hasSeenH1 = false;

        if (!$this->hasH1($headingElements)) {
            $candidate = $htmlDom->querySelector('[data-autopromote="1"]');
            if ($candidate) {
                $this->renameHeadingElement($htmlDom, $candidate, 'h1');
                $headingElements = iterator_to_array($htmlDom->querySelectorAll('h1, h2, h3, h4, h5, h6'));
            }
        }

        foreach ($headingElements as $heading) {
            $tag = strtolower($heading->tagName);
            $correctedTag = $this->getCorrectHeadingLevel($tag, $context, $hasSeenH1);

            if ($correctedTag !== $tag) {
                $this->renameHeadingElement($htmlDom, $heading, $correctedTag);
            }
        }

        return $htmlDom->saveHTML() ?: $html;
    }

    /**
     * Correct a heading level based on the current document context.
     * Prevents skipped levels (h2 → h4 becomes h2 → h3) and duplicate h1s.
     */
    private function getCorrectHeadingLevel(string $element, ?int &$context, bool &$hasSeenH1): string
    {
        $level = (int) substr($element, 1, 1);

        if ($context === null) {
            if ($element !== 'h1') {
                $context = 2;
                return 'h2';
            }
            $context = 1;
            $hasSeenH1 = true;
            return $element;
        }

        if ($hasSeenH1 && $level === 1) {
            $context = 2;
            return 'h2';
        }

        if (($level - $context) > 1) {
            $context++;
            return 'h' . $context;
        }

        $context = $level;
        return $element;
    }

    /**
     * Replace a heading element with a new one of a different tag, preserving attributes and children.
     */
    private function renameHeadingElement(\DOM\HTMLDocument $doc, \DOM\Element $element, string $newTag): void
    {
        $newElement = $doc->createElement($newTag);

        foreach ($element->attributes as $attr) {
            $newElement->setAttribute($attr->name, $attr->value);
        }

        while ($element->firstChild) {
            $newElement->appendChild($element->firstChild);
        }

        $element->parentNode->replaceChild($newElement, $element);
    }

    /**
     * @param \DOM\Element[] $headingElements
     */
    private function hasH1(array $headingElements): bool
    {
        foreach ($headingElements as $heading) {
            if (strtolower($heading->tagName) === 'h1') {
                return true;
            }
        }
        return false;
    }
}
