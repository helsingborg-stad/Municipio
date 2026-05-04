<?php

namespace Municipio\Content\HeadingStructure;

use InvalidArgumentException;
use Municipio\Content\HeadingStructure\Contracts\HeadingStructureDomAdapterInterface;

/**
 * Provides heading-structure DOM operations for PHP 8.4 HTMLDocument.
 */
class NativeDomAdapter implements HeadingStructureDomAdapterInterface
{
    private ?object $document = null;
    private string $originalHtml = '';

    /**
     * @param string $html The HTML to load.
     *
     * @return void
     */
    public function load(string $html): void
    {
        $this->originalHtml = $html;
        $this->document     = @\DOM\HTMLDocument::createFromString(
            $html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
            'UTF-8',
        );
    }

    /**
     * @return array<object>
     */
    public function getHeadingElements(): array
    {
        if (!$this->document instanceof \DOM\HTMLDocument) {
            return [];
        }

        return iterator_to_array($this->document->querySelectorAll('h1, h2, h3, h4, h5, h6'));
    }

    /**
     * @return object|null
     */
    public function findAutoPromoteCandidate(): ?object
    {
        if (!$this->document instanceof \DOM\HTMLDocument) {
            return null;
        }

        $candidate = $this->document->querySelector('[data-autopromote="1"]');

        return $candidate instanceof \DOM\Element ? $candidate : null;
    }

    /**
     * @param object $element The element to inspect.
     *
     * @return string
     */
    public function getTagName(object $element): string
    {
        if (!$element instanceof \DOM\Element) {
            throw new InvalidArgumentException('Expected instance of DOM\\Element.');
        }

        return strtolower($element->tagName);
    }

    /**
     * @param object $element The element to rename.
     * @param string $newTag The new tag name.
     *
     * @return void
     */
    public function renameHeadingElement(object $element, string $newTag): void
    {
        if (!$this->document instanceof \DOM\HTMLDocument || !$element instanceof \DOM\Element || !$element->parentNode) {
            return;
        }

        $newElement = $this->document->createElement($newTag);

        foreach ($element->attributes as $attribute) {
            $newElement->setAttribute($attribute->name, $attribute->value);
        }

        while ($element->firstChild) {
            $newElement->appendChild($element->firstChild);
        }

        $element->parentNode->replaceChild($newElement, $element);
    }

    /**
     * @return string
     */
    public function saveHtml(): string
    {
        return $this->document?->saveHtml() ?: $this->originalHtml;
    }
}
