<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues;

use AcfService\Contracts\DeleteField;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\Helper\AcfService;
use Municipio\Helper\Post;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypesInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPost;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\GetPostType;
use WpService\Contracts\UpdatePostMeta;
use WpService\Contracts\WpVerifyNonce;

/**
 * Handles the storage of form field values for schema data.
 */
class StoreFormFieldValues implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&GetPostType&GetPostMeta&UpdatePostMeta&WpVerifyNonce&GetPost&AddFilter $wpService,
        private DeleteField $acfService,
        private TryGetSchemaTypeFromPostType $schemaTypeService,
        private GetSchemaPropertiesWithParamTypesInterface $getSchemaPropertiesWithParamTypesService
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
        if (!$this->validNoncePresentInRequest($postId)) {
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

        $nameKeyMap   = $this->buildNameKeyMap($field['sub_fields']);
        $nameValueMap = $this->buildNameValueMap($postedData, $nameKeyMap);

        $schemaObject = $this->populateSchemaObjectWithPostedData($this->getSchemaObject($postId, $schemaType), $nameValueMap)->toArray();

        return $schemaObject;
    }

    private function buildNameKeyMap(array $subFields): array
    {
        $nameKeyMap = [];

        foreach ($subFields as $subField) {
            if (isset($subField['sub_fields'])) {
                $nameKeyMap[$subField['name']] = [
                    'key'         => $subField['key'],
                    'name'        => $subField['name'],
                    'sub_fields'  => $this->buildNameKeyMap($subField['sub_fields']),
                    'is_repeater' => $subField['type'] === 'repeater',
                    'type'        => AcfService::get()->getFieldObject($subField['key'])['type'] ?? null
                ];
            } else {
                $nameKeyMap[$subField['name']] = [
                    'key'  => $subField['key'],
                    'name' => $subField['name'],
                    'type' => AcfService::get()->getFieldObject($subField['key'])['type'] ?? null
                ];
            }
        }

        return $nameKeyMap;
    }

    private function buildNameValueMap(array $postedData, array $nameKeyMap): array
    {
        $nameValueMap = [];

        foreach ($nameKeyMap as $name => $spec) {
            if (!empty($spec['sub_fields']) && $spec['is_repeater']) {
                $nameValueMap[$name] = array_values(array_map(
                    fn ($item) => $this->buildNameValueMap($item, $spec['sub_fields']),
                    $postedData[$spec['key']] ?: []
                ));
            } elseif (!empty($spec['sub_fields']) && !$spec['is_repeater']) {
                $nameValueMap[$name] = $this->buildNameValueMap($postedData[$spec['key']] ?? [], $spec['sub_fields']);
            } elseif (!empty($spec['sub_fields'])) {
                $nameValueMap[$name] = $this->buildNameValueMap($postedData[$spec['key']], $spec['sub_fields']);
            } else {
                $nameValueMap[$name] = [
                    'value' => $postedData[$spec['key']] ?? null,
                    'type'  => $spec['type'] ?? null,
                ];
            }
        }

        return $nameValueMap;
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

    /**
     * Checks if a valid nonce is present in the request.
     *
     * @param int $postId The ID of the post being saved.
     * @return bool True if a valid nonce is present, false otherwise.
     */
    private function validNoncePresentInRequest(int $postId): bool
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        return !empty($_POST['_wpnonce']) && $this->wpService->wpVerifyNonce($_POST['_wpnonce'], 'update-post_' . $postId);
    }
}
