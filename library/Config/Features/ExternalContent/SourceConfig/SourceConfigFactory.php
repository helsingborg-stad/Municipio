<?php

namespace Municipio\Config\Features\ExternalContent\SourceConfig;

class SourceConfigFactory implements SourceConfigFactoryInterface
{
    public static function create(array $config): SourceConfigInterface
    {
        switch ($config['type']) {
            case 'json':
                return new JsonSourceConfig($config['file_path']);
            case 'typesense':
                return new TypesenseSourceConfig(
                    $config['typesense_api_key'],
                    $config['typesense_protocol'],
                    $config['typesense_host'],
                    $config['typesense_port'] ?? 443,
                    $config['typesense_collection']
                );
            default:
                throw new \Exception('Invalid source config type');
        }
    }
}
