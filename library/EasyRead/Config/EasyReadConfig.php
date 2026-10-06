<?php

declare(strict_types=1);

namespace Municipio\EasyRead\Config;

use AcfService\Contracts\GetField;

/**
 * Centralizes the persisted Easy Read keys to protect existing content.
 */
final class EasyReadConfig implements EasyReadConfigInterface
{
    public const ENABLED_FIELD = 'easy_reading_select';
    public const CONTENT_FIELD = 'easy_reading_content';
    public const POST_TYPES_FIELD = 'easy_reading_posttypes';
    public const READABLE_QUERY_PARAMETER = 'readable';

    public function __construct(private GetField $acfService) {}

    public function enabledField(): string
    {
        return self::ENABLED_FIELD;
    }

    public function contentField(): string
    {
        return self::CONTENT_FIELD;
    }

    public function postTypesField(): string
    {
        return self::POST_TYPES_FIELD;
    }

    public function readableQueryParameter(): string
    {
        return self::READABLE_QUERY_PARAMETER;
    }

    public function enabledPostTypes(): array
    {
        $postTypes = $this->acfService->getField($this->postTypesField(), 'option');

        if (!is_array($postTypes)) {
            return [];
        }

        return array_values(array_filter($postTypes, 'is_string'));
    }
}
