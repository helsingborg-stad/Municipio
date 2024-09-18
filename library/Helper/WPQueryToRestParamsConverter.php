<?php

namespace Municipio\Helper;

class WPQueryToRestParamsConverter implements RestParamsConverterInterface
{
    /**
     * Convert query variables to REST parameters string.
     *
     * @param array $queryVars The query variables.
     * @return string The REST parameters string.
     */
    public static function convertToRestParamsString(array $queryVars): string
    {
        $restQuery = self::mapQueryVarsToRestParams($queryVars);
        return self::formatRestParams($restQuery);
    }

    /**
     * Map query variables to REST parameters.
     *
     * @param array $queryVars The query variables.
     * @return array The REST parameters.
     */
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

    /**
     * Map a single query parameter to a REST parameter.
     *
     * @param string $key The query parameter key.
     * @param mixed $value The query parameter value.
     * @return array The REST parameter.
     */
    private static function mapQueryParam($key, $value): ?array
    {
        $restQuery = [];

        switch ($key) {
            case 'p':
                if (is_numeric($value)) {
                    $restQuery['id'] = $value;
                }
                break;
            case 'name':
            case 'pagename':
                if (is_string($value)) {
                    $restQuery['slug'] = $value;
                }
                break;
            case 'page_id':
                if (is_numeric($value)) {
                    $restQuery['page'] = $value;
                }
                break;
            case 'post__in':
            case 'post__not_in':
                if (is_array($value)) {
                    $restQuery[$key === 'post__in' ? 'include' : 'exclude'] = implode(',', $value);
                }
                break;
            case 'posts_per_page':
                if (is_numeric($value)) {
                    $value                 =  $value < 1 ? 100 : $value;
                    $restQuery['per_page'] = $value;
                }
                break;
            case 'paged':
                if (is_numeric($value)) {
                    $restQuery['page'] = $value;
                }
                break;
            case 'offset':
                if (is_numeric($value)) {
                    $restQuery['offset'] = $value;
                }
                break;
            case 'order':
                if (in_array(strtolower($value), ['asc', 'desc'])) {
                    $restQuery['order'] = strtolower($value);
                }
                break;
            case 'orderby':
                if (is_string($value)) {
                    $restQuery['orderby'] = self::mapOrderBy($value);
                }
                break;
            case 's':
                if (is_string($value)) {
                    $restQuery['search'] = $value;
                }
                break;
            case 'tax_query':
                $restQuery = self::mapTaxQuery($value, $restQuery);
                break;
            default:
                if (taxonomy_exists($key)) {
                    $terms = is_array($value) ? array_filter($value, 'is_numeric') : (is_numeric($value) ? $value : null);
                    if ($terms !== null) {
                        $restQuery[$key] = is_array($terms) ? implode(',', $terms) : $terms;
                    }
                }
                break;
        }

        return $restQuery;
    }

    private static function mapOrderBy(string $orderBy): string
    {
        if ($orderBy === 'post__in') {
            $orderBy = 'include';
        }

        return $orderBy;
    }

    /**
     * Map a tax query to a REST parameter.
     *
     * @param array $tax_query The tax query.
     * @param array $restQuery The REST parameters.
     * @return array The updated REST parameters.
     */
    private static function mapTaxQuery(array $tax_query, array $restQuery): array
    {
        foreach ($tax_query as $query) {
            if (isset($query['taxonomy'], $query['field'], $query['terms']) && $query['field'] === 'term_id') {
                $restQuery[$query['taxonomy']] = implode(',', $query['terms']);
            }
        }

        return $restQuery;
    }

    /**
     * Format REST parameters into a string.
     *
     * @param array $restQuery The REST parameters.
     * @return string The formatted REST parameters.
     */
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
