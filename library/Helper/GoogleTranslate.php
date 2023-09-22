<?php

namespace Municipio\Helper;

class GoogleTranslate
{
    public function __construct()
    {
        $words = get_field('google_exclude_words_from_translate', 'option');
        if (!empty($words)) {
            add_filter('the_content', function ($content) use ($words) {
                return $this->excludeWordsFromGoogleTranslate($content, $words);
            });
            
            add_filter('the_excerpt', function ($excerpt) use ($words) {
                return $this->excludeWordsFromGoogleTranslate($excerpt, $words);
            });
            
            add_filter('the_title', function ($title) use ($words) {
                return $this->excludeWordsFromGoogleTranslate($title, $words);
            });
        }
    }

    /**
     * Adds a span around specified words.
     * @param string $content contains the filter content
     * @param array $words contains the words that should not be translated
     * @return string
     */
    public function excludeWordsFromGoogleTranslate($content, $words) {
        if (!empty($words)) {
            $words = explode(', ', $words);
            $content = $this->replaceWordsInContent($content, $words);
        }

        return $content;
    }

    /**
     * Matches the words and change the markup accordingly.
     * @param string $content contains the filter content
     * @param array $words contains the words that should not be translated
     * @return string
     */
    private function replaceWordsInContent($content, $words) {
        preg_match_all("/<[^>]+>|[^<]+/", $content, $matches);
    
        $output = '';
    
        foreach ($matches[0] as $match) {
            if (strpos($match, '<') === 0) {
                $output .= $match;
            } else {
                foreach ($words as $word) {
                    $pattern = "/(?<!<span translate=\"no\">)(?<!<\/span>)(\b" . preg_quote($word) . "\b)/i";
                    $match = preg_replace($pattern, ' <span translate="no">$1</span>', $match);
                }
                $output .= $match;
            }
        }
    
        return $output;
    }
}

