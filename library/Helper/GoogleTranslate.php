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
                return $this->shouldReplaceWords($content, $words);
            });

            add_filter('the_excerpt', function ($excerpt) use ($words) {
                return $this->shouldReplaceWords($excerpt, $words);
            });

            add_filter('the_title', function ($title) use ($words) {
                return $this->shouldReplaceWords($title, $words);
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
    public function shouldReplaceWords(string $content = '', string $words = '')
    {
        if (!empty($words)) {
            $words   = explode(', ', $words);
            $content = $this->replaceWordsInContent($content, $words);
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
    private function replaceWordsInContent(string $content, array $words)
    {
        preg_match_all("/<[^>]+>|[^<]+/", $content, $matches);

        $output = '';

        if (!empty($matches[0]) && is_array($matches[0])) {
            foreach ($matches[0] as $match) {
                if (strpos($match, '<') === 0) {
                    $output .= $match;
                } else {
                    foreach ($words as $word) {
                        $pattern = "/(?<!<span translate=\"no\">)(?<!<\/span>)\b" .
                        preg_quote($word) . "\b(?![^<>]*>)/i";
                        $match   = preg_replace($pattern, ' <span translate="no">$0</span>', $match);
                    }
                    $output .= $match;
                }
            }
        }

        return $output;
    }
}
