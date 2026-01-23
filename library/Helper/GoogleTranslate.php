<?php

namespace Municipio\Helper;

/**
 * Class GoogleTranslate
 */
class GoogleTranslate
{
    /**
     * Adds filters
     */
    public function __construct()
    {
        $words = get_field('google_exclude_words_from_translate', 'option');
        if (!empty($words) && is_string($words)) {
            add_filter('the_content', function ($content) use ($words) {
                return $this->shouldWrapWords($content, $words);
            });

            add_filter('the_excerpt', function ($excerpt) use ($words) {
                return $this->shouldWrapWords($excerpt, $words);
            });

            add_filter('the_title', function ($title) use ($words) {
                return $this->shouldWrapWords($title, $words);
            });
        }
    }

    /**
     * Adds a span around specified words.
     *
     * @param string $content contains the filter content
     * @param string $words contains the words that should not be translated
     *
     * @return string
     */
    public function shouldWrapWords(string $content = '', string $words = '')
    {
        if (!empty($words)) {
            $words = explode(', ', $words);
            $content = static::wrapWordsInContent($content, $words);
        }

        return $content;
    }

    /**
     * Matches the words and change the markup accordingly.
     *
     * @param string $content contains the filter content
     * @param array $words contains the words that should not be translated
     *
     * @return string
     */

    public static function wrapWordsInContent(string $content, array $words): string
    {
        // Split content into HTML tags and text segments
        preg_match_all('/<[^>]+>|[^<]+/', $content, $segments);

        if (empty($segments[0]) || !is_array($segments[0])) {
            return $content;
        }

        $result = '';
        foreach ($segments[0] as $index => $segment) {
            if (self::isHtmlTag($segment)) {
                $result .= $segment;
                continue;
            }

            if (self::isInsideNoTranslateSpan($segments[0], $index)) {
                $result .= $segment;
                continue;
            }

            $result .= self::wrapWordsWithNoTranslate($segment, $words);
        }

        return $result;
    }

    /**
     * Checks if the segment is an HTML tag.
     */
    private static function isHtmlTag(string $segment): bool
    {
        return strpos($segment, '<') === 0;
    }

    /**
     * Checks if the segment is inside a no-translate span context.
     */
    private static function isInsideNoTranslateSpan(array $segments, int $index): bool
    {
        return $index > 0 && self::endsWithOpeningNoTranslateSpan($segments[$index - 1]) && self::startsWithClosingSpan($segments[$index + 1] ?? '');
    }

    /**
     * Wraps specified words in the segment with a no-translate span.
     */
    private static function wrapWordsWithNoTranslate(string $segment, array $words): string
    {
        foreach ($words as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $segment = preg_replace_callback(
                $pattern,
                function ($matches) {
                    return '<span translate="no">' . $matches[0] . '</span>';
                },
                $segment,
            );
        }
        return $segment;
    }

    private static function endsWithOpeningNoTranslateSpan(string $string): bool
    {
        // Check for <span translate="no"> at the end
        return preg_match('/<span\s+translate="no">$/', $string) === 1;
    }

    private static function startsWithClosingSpan(string $string): bool
    {
        // Check for </span> at the start
        return preg_match('/^<\/span>/', $string) === 1;
    }
}
