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
     * @return string
     */
    public function excludeWordsFromGoogleTranslate($content, $words) {
        if (!empty($words)) {
            $words = explode(', ', $words);
        
            foreach ($words as $word) {
                $pattern = "/(?<!<span translate=\"no\">)(?<!<\/span>)(\b" . preg_quote($word) . "\b)/i";
                $content = preg_replace($pattern, '<span translate="no">$1</span>', $content);
            }
        }
        
        
        

        return $content;
    }
}

