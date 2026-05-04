<?php

namespace Municipio\Content\HeadingStructure\Contracts;

/**
 * Describes DOM operations required by the heading structure formatter.
 */
interface HeadingStructureDomAdapterInterface
{
    /**
     * Loads the provided HTML into the adapter.
     *
     * @param string $html The HTML to load.
     *
     * @return void
     */
    public function load(string $html): void;

    /**
     * Returns heading elements from the current document.
     *
     * @return array<object>
     */
    public function getHeadingElements(): array;

    /**
     * Returns the first element marked for auto-promotion.
     *
     * @return object|null
     */
    public function findAutoPromoteCandidate(): ?object;

    /**
     * Returns the lowercased tag name of the provided element.
     *
     * @param object $element The element to inspect.
     *
     * @return string
     */
    public function getTagName(object $element): string;

    /**
     * Renames an element while preserving attributes and children.
     *
     * @param object $element The element to rename.
     * @param string $newTag The new tag name.
     *
     * @return void
     */
    public function renameHeadingElement(object $element, string $newTag): void;

    /**
     * Serializes the loaded HTML document.
     *
     * @return string
     */
    public function saveHtml(): string;
}
