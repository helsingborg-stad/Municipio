<?php

namespace Municipio\ExternalContent\Config;

use AcfService\Contracts\GetField;
use AcfService\Contracts\GetFields;
use Municipio\SchemaData\Utils\GetSchemaTypeFromPostType;

class TryCreateConfigUsingAcf implements ConfigFactoryInterface
{
    private const ACF_FIELD_NAME = 'external_content_source';

    public function __construct(
        private string $postType,
        private string $schemaType,
        private GetField $acfService,
    ) {
    }

    /**
     * Create the source config.
     *
     * @return SourceConfigInterface The created source config.
     * @throws \Exception If the ACF configuration or schema type is missing.
     * @throws \Exception If the source config type is unknown.
     */
    public function create(): SourceConfigInterface
    {
        $acfConfig = $this->acfService->getField(self::ACF_FIELD_NAME, $this->postType . '_options');

        if (empty($acfConfig)) {
            throw new \Exception('Missing ACF configuration for post type: ' . $this->postType);
        }

        if ($acfConfig['type'] === 'typesense') {
            return new \Municipio\ExternalContent\Config\Providers\TypesenseSourceConfig(
                $this->postType,
                $this->schemaType,
                $acfConfig['typesense_api_key'],
                $acfConfig['typesense_host'],
                $acfConfig['typesense_collection']
            );
        }

        if ($acfConfig['type'] === 'localFile') {
            return new \Municipio\ExternalContent\Config\Providers\JsonFileSourceConfig(
                $this->postType,
                $this->schemaType,
                $acfConfig['file_path']
            );
        }

        throw new \Exception('Unknown source config type for post type: ' . $this->postType);
    }
}
