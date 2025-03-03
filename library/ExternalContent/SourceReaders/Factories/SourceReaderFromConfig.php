<?php

namespace Municipio\ExternalContent\SourceReaders\Factories;

use Municipio\ExternalContent\Config\SourceConfigInterface;
use Municipio\ExternalContent\JsonToSchemaObjects\SimpleJsonConverter;
use Municipio\ExternalContent\SourceReaders\FileSystem\FileSystem;
use Municipio\ExternalContent\SourceReaders\HttpApi\ApiGET;
use Municipio\ExternalContent\SourceReaders\HttpApi\TypesenseApi\TypesenseApi;
use Municipio\ExternalContent\SourceReaders\JsonFileSourceReader;
use Municipio\ExternalContent\SourceReaders\SourceReader;
use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\ExternalContent\SourceReaders\TypesenseSourceReader;
use Municipio\Helper\WpService;

/**
 * Class SourceReaderFromConfig
 *
 * Create a SourceReader based on a SourceConfig
 */
class SourceReaderFromConfig implements SourceReaderFromConfigInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function create(SourceConfigInterface $config): SourceReaderInterface
    {
        return match ($config->getSourceType()) {
            'json' => new JsonFileSourceReader($config->getSourceJsonFilePath(), new FileSystem(), new SimpleJsonConverter()),
            'typesense' => new TypesenseSourceReader($this->getTypesenApi($config), '', new SimpleJsonConverter()),
            default => new SourceReader()
        };
    }

    /**
     * Get Typesense API
     *
     * @param SourceConfigInterface $config
     * @return ApiGET
     */
    private function getTypesenApi(SourceConfigInterface $config): ApiGET
    {
        return new TypesenseApi($config, WpService::get());
    }
}
