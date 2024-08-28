<?php

namespace Municipio\ExternalContent\Config;

use AcfService\Contracts\GetField;
use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\SchemaData\Utils\GetSchemaTypeFromPostTypeInterface;

class TryCreateConfigUsingAcf implements ConfigFactoryInterface
{
    private const ACF_FIELD_NAME = 'external_content_source';

    public function __construct(
        private GetField $acfService,
        private TryGetSchemaTypeFromPostType $tryGetSchemaTypeFromPostType
    ) {
    }

    /**
     * Create the source config.
     *
     * @return SourceConfigInterface The created source config.
     * @throws \Exception If the ACF configuration or schema type is missing.
     * @throws \Exception If the source config type is unknown.
     */
    public function create(string $postType): SourceConfigInterface
    {
        $schemaType = $this->tryGetSchemaTypeFromPostType->tryGetSchemaTypeFromPostType($postType);
        $acfConfig  = $this->acfService->getField(self::ACF_FIELD_NAME, $postType . '_options');

        if (empty($acfConfig)) {
            throw new \Exception('Missing ACF configuration for post type: ' . $postType);
        }

        if ($acfConfig['type'] === 'typesense') {
            return new \Municipio\ExternalContent\Config\Providers\TypesenseSourceConfig(
                $postType,
                $schemaType,
                $acfConfig['typesense_api_key'],
                $acfConfig['typesense_host'],
                $acfConfig['typesense_collection']
            );
        }

        if ($acfConfig['type'] === 'localFile') {
            return new \Municipio\ExternalContent\Config\Providers\JsonFileSourceConfig(
                $postType,
                $schemaType,
                $acfConfig['file_path']
            );
        }

        throw new \Exception('Unknown source config type for post type: ' . $postType);
    }
}
