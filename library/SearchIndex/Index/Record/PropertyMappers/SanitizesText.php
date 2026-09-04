<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

/**
 * Normalizes text for use in search records.
 */
trait SanitizesText
{
    /**
     * Remove markup and normalize whitespace.
     */
    private function sanitizeText(string $text): string
    {
        $text = preg_replace('/<(script|style|noscript)\b[^>]*>.*?<\/\1>/is', '', $text) ?? ''; // Remove script, style, and noscript tags along with their contents
        $text = preg_replace('/\s+/', ' ', strip_tags($text)) ?? ''; // Remove all remaining HTML tags and normalize whitespace
        return trim($text);
    }
}