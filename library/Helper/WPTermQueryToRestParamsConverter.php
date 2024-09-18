<?php

namespace Municipio\Helper;

class WPTermQueryToRestParamsConverter implements RestParamsConverterInterface
{
    public static function convertToRestParamsString(array $queryVars): string
    {
        $restQuery = self::mapQueryVarsToRestParams($queryVars);
        return self::formatRestParams($restQuery);
    }

    private static function mapQueryVarsToRestParams(array $queryVars): array
    {
        $restQuery = [];

        foreach ($queryVars as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $mappedParam = self::mapQueryParam($key, $value);

            if ($mappedParam !== null) {
                $restQuery = array_merge($restQuery, $mappedParam);
            }
        }

        return $restQuery;
    }

    private static function mapQueryParam($key, $value): ?array
    {
        $restQuery = [];

        switch ($key) {
            case 'object_ids':
                if (is_array($value)) {
                    $restQuery['post'] = implode(',', array_filter($value, 'is_numeric'));
                } elseif (is_numeric($value)) {
                    $restQuery['post'] = $value;
                }
                break;
            case 'taxonomy':
                if (is_array($value)) {
                    $restQuery['taxonomy'] = implode(',', $value);
                } elseif (is_string($value)) {
                    $restQuery['taxonomy'] = $value;
                }
                break;
            case 'order':
                if (in_array(strtolower($value), ['asc', 'desc'])) {
                    $restQuery['order'] = strtolower($value);
                }
                break;
            case 'orderby':
                if (is_string($value)) {
                    $restQuery['orderby'] = $value;
                }
                break;
            case 'hide_empty':
                if (is_bool($value)) {
                    $restQuery['hide_empty'] = $value ? 'true' : 'false';
                }
                break;
            case 'include':
            case 'exclude':
            case 'parent':
            case 'parent_exclude':
                if (is_array($value)) {
                    $restQuery[$key] = implode(',', array_filter($value, 'is_numeric'));
                } elseif (is_numeric($value)) {
                    $restQuery[$key] = $value;
                }
                break;
            case 'slug':
                if (is_string($value)) {
                    $restQuery['slug'] = $value;
                }
                break;
            case 'offset':
            case 'number':
                if (is_numeric($value)) {
                    $restQuery[$key] = $value;
                }
                break;
            case 'search':
                if (is_string($value)) {
                    $restQuery['search'] = $value;
                }
                break;
                // Add more cases as needed...
        }

        return $restQuery;
    }

    private static function formatRestParams(array $restQuery): string
    {
        return implode('&', array_map(
            function ($key, $value) {
                return sprintf('%s=%s', urlencode($key), urlencode($value));
            },
            array_keys($restQuery),
            $restQuery
        ));
    }
}
