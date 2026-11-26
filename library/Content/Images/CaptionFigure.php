<?php

declare(strict_types=1);

namespace Municipio\Content\Images;

/**
 * Handles the <figure> that the classic editor [caption] shortcode wraps around an image.
 *
 * The image component that replaces the image is itself a <figure> with caption and
 * alignment classes. Keeping the caption figure around it would nest two floating
 * figures, which collapses the image to zero width, so the caption figure is unwrapped.
 */
final class CaptionFigure
{
    /**
     * Get the caption figure that wraps an image, if the figure contains nothing but
     * the image, optionally wrapped in a link, and its figcaption.
     *
     * @param \DOMElement $image
     * @return \DOMElement|null
     */
    public static function find(\DOMElement $image): ?\DOMElement
    {
        $inner  = $image;
        $figure = $image->parentNode;

        if ($figure instanceof \DOMElement && strtolower($figure->nodeName) === 'a') {
            $inner  = $figure;
            $figure = $figure->parentNode;
        }

        $isCaptionFigure = $figure instanceof \DOMElement
            && strtolower($figure->nodeName) === 'figure'
            && $figure->parentNode instanceof \DOMNode
            && self::containsOnly($figure, $inner);

        return $isCaptionFigure ? $figure : null;
    }

    /**
     * Replace a caption figure with its children.
     *
     * @param \DOMElement $figure
     * @return void
     */
    public static function unwrap(\DOMElement $figure): void
    {
        while ($figure->firstChild) {
            $figure->parentNode->insertBefore($figure->firstChild, $figure);
        }

        $figure->parentNode->removeChild($figure);
    }

    /**
     * Whether an element contains nothing but the given node, a figcaption and whitespace.
     *
     * @param \DOMElement $element
     * @param \DOMNode $node
     * @return bool
     */
    private static function containsOnly(\DOMElement $element, \DOMNode $node): bool
    {
        foreach ($element->childNodes as $child) {
            $isAllowed = $child === $node
                || ($child instanceof \DOMElement && strtolower($child->nodeName) === 'figcaption')
                || ($child instanceof \DOMText && trim($child->textContent) === '')
                || $child instanceof \DOMComment;

            if (!$isAllowed) {
                return false;
            }
        }

        return true;
    }
}
