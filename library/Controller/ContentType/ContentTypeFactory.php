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
 * should extend this class to gain basic functionality.
 * An example of a simple content type is the default type 'Place'.
 *
 * Complex content types:
 * ----------------------
 * A complex content type is a class that needs to load other types and
 * it should extend this class but also implement the 'ContentTypeComplexInterface'
 * interface.
 * An example of a complex purpose is the default 'Event'.
 *
 */
abstract class ContentTypeFactory implements ContentTypeComponentInterface
{
    protected string $key;
    protected string $label;
    protected array $schemaParams;

        /**
         * ContentTypeFactory constructor.
         *
         * @param string $key   The key of the content type.
         * @param string $label The label of the content type.
         */
    public function __construct(string $key, string $label)
    {
        $this->key          = $key;
        $this->label        = $label;
        $this->schemaParams = $this->applySchemaParamsFilter();
    }
    /**
     * Apply the 'Municipio/ContentType/schemaParams' filter to the schema parameters.
     *
     * @return array The filtered schema parameters.
     */
    protected function applySchemaParamsFilter(): array
    {
        $params = $this->setSchemaParams();

        return apply_filters('Municipio/ContentType/schemaParams', $params, $this->key);
    }

    /**
     * Abstract method to set schema parameters.
     * Must be implemented by subclasses to define their specific schema parameters.
     *
     * @return array The schema parameters.
     */
    abstract protected function setSchemaParams(): array;

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
     * Get the schema parameters.
     *
     * @return array|null The schema parameters.
     */
    public function getSchemaParams(): ?array
    {
        return $this->schemaParams;
    }
    /**
     * Get the structured data for a post.
     *
     * @param int $postId The ID of the post.
     * @return array|null The structured data for the post.
     */
    public function getStructuredData(int $postId): ?array
    {
        return [];
    }
}
