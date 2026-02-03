<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

/**
 * Archive default values
 *
 * Centralized constants for archive configuration defaults.
 * Provides single source of truth for default values used across
 * archive mappers and async attributes provider.
 *
 * Note: This uses a class with constants rather than an enum because
 * some defaults share the same value (e.g., DATE_SOURCE and ORDER_BY
 * both default to 'post_date'), which is not allowed in PHP enums.
 */
class ArchiveDefaults
{
    /**
     * Default query variables prefix for archive filtering
     */
    public const QUERY_VARS_PREFIX = 'archive_';

    /**
     * Default number of columns in grid layout
     */
    public const NUMBER_OF_COLUMNS = 3;

    /**
     * Default date format display
     */
    public const DATE_FORMAT = 'date-time';

    /**
     * Default date source field
     */
    public const DATE_SOURCE = 'post_date';

    /**
     * Default design/style for archive display
     */
    public const DESIGN = 'card';

    /**
     * Default number of posts per page
     */
    public const POSTS_PER_PAGE = 10;

    /**
     * Default order direction
     */
    public const ORDER = 'desc';

    /**
     * Default order by field
     */
    public const ORDER_BY = 'post_date';

    /**
     * Default display featured image setting
     */
    public const DISPLAY_FEATURED_IMAGE = false;

    /**
     * Default display reading time setting
     */
    public const DISPLAY_READING_TIME = false;
}
