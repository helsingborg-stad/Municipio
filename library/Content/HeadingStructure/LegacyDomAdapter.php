<?php

namespace Municipio\Content\HeadingStructure;

use DOMDocument;
use DOMElement;
use DOMXPath;
use InvalidArgumentException;
use Municipio\Content\HeadingStructure\Contracts\HeadingStructureDomAdapterInterface;

/**
 * Provides heading-structure DOM operations for PHP 8.3 DOMDocument.
 */
class LegacyDomAdapter implements HeadingStructureDomAdapterInterface
{
    private ?DOMDocument $document = null;
    private string $originalHtml = '';

    /**
     * @param string $html The HTML to load.
     *
     * @return void
     */
    public function load(string $html): void
    {
        $this->originalHtml = $html;

        $document                  = new DOMDocument('1.0', 'UTF-8');
        $document->encoding        = 'UTF-8';
        $document->preserveWhiteSpace = true;

        $previousUseInternalErrors = libxml_use_internal_errors(true);
        $document->loadHTML(
            sprintf('<?xml encoding="UTF-8">%s', $html),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousUseInternalErrors);

        foreach (iterator_to_array($document->childNodes) as $childNode) {
            if ($childNode->nodeType === XML_PI_NODE) {
                $document->removeChild($childNode);
            }
        }

        $this->document = $document;
    }

    /**
     * @return array<object>
     */
    public function getHeadingElements(): array
    {
        if (!$this->document instanceof DOMDocument) {
            return [];
        }

        $nodeList = (new DOMXPath($this->document))->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');

        return $nodeList === false ? [] : iterator_to_array($nodeList);
    }

    /**
     * @return object|null
     */
    public function findAutoPromoteCandidate(): ?object
    {
        if (!$this->document instanceof DOMDocument) {
            return null;
        }

        $nodeList = (new DOMXPath($this->document))->query('//*[@data-autopromote="1"]');
        $candidate = $nodeList === false ? null : $nodeList->item(0);

        return $candidate instanceof DOMElement ? $candidate : null;
    }

    /**
     * @param object $element The element to inspect.
     *
     * @return string
     */
    public function getTagName(object $element): string
    {
        if (!$element instanceof DOMElement) {
            throw new InvalidArgumentException('Expected instance of DOMElement.');
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
        if (!$this->document instanceof DOMDocument || !$element instanceof DOMElement || !$element->parentNode) {
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
        return $this->document?->saveHTML() ?: $this->originalHtml;
    }
}
