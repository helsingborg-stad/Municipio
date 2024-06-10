<?php

namespace Municipio\Controller\ContentType;

/**
 * The ContentTypeFactory Class
 * ----------------------------
 * This class is a basic implementation of the 'ContentTypeComponentInterface' interface.
 *
 * It provides default methods and properties for a content type and
 * is designed to be a base class for all content type classes in the project.
 *
 * Simple content types:
 * ---------------------
 * A simple content type class that doesn't need to load other types
 * An example of a simple content type is the default type 'Place'.
 *
 * Complex content types:
 * ----------------------
 * A complex content type is a class that needs to load other types
 * An example of a complex purpose is the default 'Event'.
 *
 */
abstract class ContentTypeFactory implements ContentTypeComponentInterface
{
    protected string $key;
    protected string $label;
    protected array $secondaryContentType = [];
    protected array $schemaParams;

    /**
     * ContentTypeFactory constructor.
     *
     * @param string $key   The key of the content type.
     * @param string $label The label of the content type.
     */

    public function __construct(string $key, string $label)
    {
        $this->key   = $key;
        $this->label = $label;
    }
    /**
     * Get the label of the content type.
     *
     * @return string The label of the content type.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the key of the content type.
     *
     * @return string The key of the content type.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the view for the content type.
     *
     * @return string The view for the content type.
     */
    public function getView(): string
    {
        return "content-type-{$this->getKey()}";
    }

    /**
     * Add hooks for the content type.
     */
    public function addHooks(): void
    {
    }
}
