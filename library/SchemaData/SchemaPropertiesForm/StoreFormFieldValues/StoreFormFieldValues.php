<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\Helper\Post;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper\FieldMapper;
use Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\NonceValidation\PostNonceValidatorInterface;
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
     * Constructor.
     */
    public function __construct(
        private AddAction&GetPostType&GetPostMeta&UpdatePostMeta&GetPost&AddFilter $wpService,
        private TryGetSchemaTypeFromPostType $schemaTypeService,
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypesService,
        private PostNonceValidatorInterface $nonceValidationService,
        private FieldMapper $fieldMapper
    ) {
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
        return $this->populateSchemaObjectWithPostedData($schemaObject, $mappedFields);
    }

    private function populateSchemaObjectWithPostedData(BaseType $schemaObject, array $nameValueMap): BaseType
    {
        $schemaProperties = $this->getSchemaPropertiesWithParamTypesService->getSchemaPropertiesWithParamTypes($schemaObject::class);
        $schemaProperties = [...$schemaProperties, '@id' => ['string']];

        foreach ($nameValueMap as $propertyName => $spec) {
            $value     = $spec['value'] ?? null;
            $fieldType = $spec['type'] ?? null;

            if (is_string($value) && json_validate(stripslashes($value))) {
                $value = json_decode(stripslashes($value), true);
            }

            if (!array_key_exists($propertyName, $schemaProperties)) {
                continue;
            }

            if ($fieldType === 'gallery' && is_array($value) && in_array('ImageObject[]', $schemaProperties[$propertyName])) {
                $imageIds = array_filter($value, 'is_numeric');
                $schemaObject->setProperty($propertyName, array_map(
                    function ($imageId) {
                        return Schema::imageObject()
                            ->identifier($imageId)
                            ->name($this->wpService->getPost($imageId)->post_title)
                            ->url(wp_get_attachment_image_url($imageId, 'full'))
                            ->caption(wp_get_attachment_caption($imageId))
                            ->description(get_post_meta($imageId, '_wp_attachment_image_alt', true));
                    },
                    $imageIds
                ));
            } elseif (is_array($value) && !empty($value['@type'])) {
                $schemaObject->setProperty(
                    $propertyName,
                    $this->populateSchemaObjectWithPostedData(
                        Schema::{lcfirst($value['@type'])}(),
                        $value
                    )
                );
            } elseif (is_array($value) && array_key_exists('row-0', $value) && !empty($value['row-0']['@type'])) {
                $schemaObject->setProperty(
                    $propertyName,
                    array_map(
                        fn ($item) => $this->populateSchemaObjectWithPostedData(
                            Schema::{lcfirst($item['@type'])}(),
                            $item
                        ),
                        $value
                    )
                );
            } elseif (is_array($value) && in_array('GeoCoordinates', $schemaProperties[$propertyName]) && !empty($value['lat'] && !empty($value['lng']) && !empty($value['address']))) {
                $schemaObject->setProperty(
                    $propertyName,
                    Schema::geoCoordinates()->latitude($value['lat'])->longitude($value['lng'])->address($value['address'])
                );
            } elseif (is_array($value) && in_array('Place', $schemaProperties[$propertyName]) && !empty($value['lat'] && !empty($value['lng']) && !empty($value['address']))) {
                $schemaObject->setProperty(
                    $propertyName,
                    Schema::place()->latitude($value['lat'])->longitude($value['lng'])->address($value['address'])
                );
            } elseif (is_array($value)) {
                $schemaObject->setProperty($propertyName, array_map(fn ($item) => $item, $value));
            } elseif (is_string($value) && in_array('\DateTimeInterface', $schemaProperties[$propertyName]) && @strtotime($value)) {
                $schemaObject->setProperty($propertyName, new \DateTime($value));
            } elseif (is_string($value)) {
                $schemaObject->setProperty($propertyName, $value);
            } else {
                $schemaObject->setProperty($propertyName, $value);
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
