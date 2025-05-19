<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\Helper\Post;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper\FieldMapper;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper\FieldMapperInterface;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\NonceValidation\PostNonceValidatorInterface;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler\SchemaPropertyHandlerInterface;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPost;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPostType;
use WpService\Contracts\UpdatePostMeta;

/**
 * Handles the storage of form field values for schema data.
 */
class StoreFormFieldValues implements Hookable
{
    /**
     * @var SchemaPropertyHandlerInterface[]
     */
    private array $propertyHandlers = [];

    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&GetPostType&GetPostMeta&UpdatePostMeta&GetPost&AddFilter $wpService,
        private TryGetSchemaTypeFromPostType $schemaTypeService,
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypesService,
        private PostNonceValidatorInterface $nonceValidationService,
        private FieldMapperInterface $fieldMapper
    ) {
        $this->propertyHandlers = [
            new SchemaPropertyHandler\GalleryHandler($this->wpService),
            new SchemaPropertyHandler\RepeaterWithSchemaObjectsHandler($this),
            new SchemaPropertyHandler\GroupWithSchemaObjectHandler($this),
            new SchemaPropertyHandler\GeoCoordinatesHandler(),
            new SchemaPropertyHandler\EmailHandler(),
            new SchemaPropertyHandler\DateTimeHandler(),
            new SchemaPropertyHandler\DateHandler(),
            new SchemaPropertyHandler\UrlHandler(),
            new SchemaPropertyHandler\TextHandler(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('acf/update_value/name=schemaData', [$this, 'saveSchemaData'], 100, 4);
    }

    /**
     * Saves the schema data for a given post ID.
     */
    public function saveSchemaData(mixed $value, string|int $postId, array $field, mixed $original): mixed
    {
        if (!$this->nonceValidationService->isValid($postId, $_POST['_wpnonce'] ?? null)) {
            return $value;
        }

        $schemaType = $this->getSchemaType($this->wpService->getPostType($postId));

        if (!$schemaType) {
            return $value;
        }

        $postedData = $_POST['acf'][$field['key']] ?? [];

        if (empty($postedData)) {
            return $value;
        }

        $mappedFields = $this->fieldMapper->getMappedFields($field['sub_fields'], $postedData);
        $schemaObject = $this->getSchemaObject($postId, $schemaType);
        $schemaObject = $this->populateSchemaObjectWithPostedData($schemaObject, $mappedFields);
        return $schemaObject->toArray();
    }

    public function populateSchemaObjectWithPostedData(BaseType $schemaObject, array $mappedFields): BaseType
    {
        $schemaProperties = $this->getSchemaPropertiesWithParamTypesService->getSchemaPropertiesWithParamTypes($schemaObject::class);
        $schemaProperties = [...$schemaProperties, '@id' => ['string']];

        foreach ($mappedFields as $mappedField) {
            $value        = $mappedField->getValue();
            $fieldType    = $mappedField->getType() ?? '';
            $propertyName = $mappedField->getName();

            if (is_string($value) && json_validate(stripslashes($value))) {
                $value = json_decode(stripslashes($value), true);
            }

            if (!array_key_exists($propertyName, $schemaProperties)) {
                continue;
            }

            $propertyTypes = $schemaProperties[$propertyName];

            foreach ($this->propertyHandlers as $handler) {
                $supports = $handler->supports($propertyName, $fieldType, $value, $propertyTypes);
                if ($supports) {
                    $schemaObject = $handler->handle($schemaObject, $propertyName, $value);
                    break;
                }
            }
        }

        return $schemaObject;
    }

    /**
     * Retrieves the schema type for a given post type.
     *
     * @param string $postType The post type to retrieve the schema type for.
     * @return string|null The schema type, or null if not found.
     */
    private function getSchemaType(string $postType): ?string
    {
        return $this->schemaTypeService->tryGetSchemaTypeFromPostType($postType);
    }

    /**
     * Retrieves the schema object for a given post ID.
     *
     * @param int $postId The ID of the post to retrieve the schema object for.
     * @return BaseType The schema object.
     */
    private function getSchemaObject(int $postId, string $schemaType): BaseType
    {
        $schemaObject = Post::preparePostObject($this->wpService->getPost($postId))->getSchema();

        if ($schemaObject->getType() === $schemaType) {
            return $schemaObject;
        }

        return Schema::{lcfirst($schemaType)}();
    }
}
