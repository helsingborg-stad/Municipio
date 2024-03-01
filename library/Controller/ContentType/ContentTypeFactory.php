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
        $this->key          = $key;
        $this->label        = $label;
        $this->schemaParams = $this->applySchemaParamsFilter();
    }
    /**
     * Get the secondary content type.
     *
     * @return array The secondary content type.
     */
    public function getSecondaryContentType(): array
    {
        return $this->secondaryContentType;
    }
    /**
     * Apply the 'Municipio/ContentType/schemaParams' filter to the schema parameters.
     *
     * @return array The filtered schema parameters.
     */
    protected function applySchemaParamsFilter(): array
    {
        $params = $this->schemaParams();

        return apply_filters('Municipio/ContentType/schemaParams', $params, $this->key);
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
     * Add a secondary content type.
     *
     * @param \Municipio\Controller\ContentType\ContentTypeFactory $contentType The content type to add.
     * @return void
     */
    public function addSecondaryContentType(
        \Municipio\Controller\ContentType\ContentTypeFactory $contentType
    ): void {
        if (\Municipio\Helper\ContentType::validateSimpleContentType($contentType, $this)) {
            $this->secondaryContentType[] = $contentType;
        }
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
     * Add hooks for the content type.
     */
    public function addHooks(): void
    {
    }
    /**
     * Define schema parameters in subclasses.
     */
    abstract protected function schemaParams(): array;

/**
 * Template method for getting structured data, with legacy fallback.
 */
    public function getStructuredData(int $postId): ?array
    {
        $schemaParams = $this->schemaParams();
        $schemaData   = (array) get_field('schema', $postId);

        $graph  = new \Spatie\SchemaOrg\Graph();
        $entity = $this->getSchemaEntity($graph);

        // Fallback to legacy method if schema parameters or data are empty
        if (empty($schemaParams) || empty($schemaData)) {
            return $this->legacyGetStructuredData($postId, $entity);
        }

        try {
            foreach ($schemaParams as $key => $param) {
                $value = $schemaData[$key] ?? null;
                if (empty($value)) {
                    continue;
                }

                // Handle ImageObject schema type
                if ($param['schemaType'] === 'ImageObject' && !$this->processImageObject($value)) {
                    continue;
                }

                if (method_exists($entity, $key)) {
                    call_user_func([$entity, $key], $value);
                }
            }
            return $graph->toArray();
        } catch (\Exception $e) {
            // Fallback to legacy method in case of an error
            return $this->legacyGetStructuredData($postId, $entity);
        }
    }

/**
 * Processes an ImageObject, returning the image URL or false if unavailable.
 *
 * @param mixed $value The value to process, expected to be an array with an 'ID' key.
 * @return mixed The image URL if successful, or false if not.
 */
    protected function processImageObject(&$value)
    {
        if (isset($value['ID'])) {
            $attachmentId = $value['ID'];
            $value        = wp_get_attachment_image_url($attachmentId, 'full');
        }
        return $value ? $value : false;
    }

    /**
     * Get the schema entity from the graph.
     *
     * @param \Spatie\SchemaOrg\Graph $graph The schema graph.
     * @return \Spatie\SchemaOrg\BaseType The schema entity corresponding to this content type.
     */
    protected function getSchemaEntity(\Spatie\SchemaOrg\Graph $graph)
    {
        $methodName = $this->getKey();
        return $graph->$methodName();
    }
    /**
     * Legacy method for getting structured data.
     * This method should be implemented to handle structured data according to the old logic.
     */
    abstract protected function legacyGetStructuredData(int $postId, $entity): ?array;
}
