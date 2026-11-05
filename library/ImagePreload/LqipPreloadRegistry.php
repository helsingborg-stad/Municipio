<?php

declare(strict_types=1);

namespace Municipio\ImagePreload;

/** Collects the unique LQIP URLs used by Image components during a request. */
class LqipPreloadRegistry
{
    private const DEFAULT_MAX_PRELOADS = 10;
    private const DEFAULT_FETCH_PRIORITY = 'high';

    /** @var array<string, string> */
    private static array $urls = [];
    private static ?LqipPreloadConfigInterface $config = null;

    public static function configure(LqipPreloadConfigInterface $config): void
    {
        self::$config = $config;
    }

    public static function add(string $url): void
    {
        if ($url !== '') {
            self::$urls[$url] = $url;
        }
    }

    /** @return array<int, string> */
    public static function all(): array
    {
        if (self::$config !== null && !self::$config->isEnabled()) {
            return [];
        }

        $maxPreloads = self::$config?->maxPreloads() ?? self::DEFAULT_MAX_PRELOADS;

        return array_slice(array_values(self::$urls), 0, $maxPreloads);
    }

    public static function fetchPriority(): string
    {
        return self::$config?->fetchPriority() ?? self::DEFAULT_FETCH_PRIORITY;
    }

    /** Reset the request-local registry. */
    public static function reset(): void
    {
        self::$urls = [];
        self::$config = null;
    }
}
