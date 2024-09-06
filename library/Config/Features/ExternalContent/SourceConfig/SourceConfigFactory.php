<?php

namespace Municipio\Config\Features\ExternalContent\SourceConfig;

class SourceConfigFactory implements SourceConfigFactoryInterface
{
    public static function create(array $config): SourceConfigInterface
    {
        switch ($config['source_type']) {
            case 'json':
                return new JsonSourceConfig($config['source_json_file_path']);
            case 'typesense':
                return new TypesenseSourceConfig(
                    $config['source_typesense_api_key'],
                    $config['source_typesense_protocol'],
                    $config['source_typesense_host'],
                    $config['source_typesense_port'] ?? 443,
                    $config['source_typesense_collection']
                );
            default:
                throw new \Exception('Invalid source config type');
        }
    }
}
