<?php

/**
 * Swedish stemmer based on @link(http://snowball.tartarus.org/algorithms/swedish/stemmer.html)
 */

namespace Intranet\SearchWp\Stemmer;

class Swedish
{
    private $regexVowels = '[aeiouyäåö]';
    private $regexNonVowels = '[^aeiouyäåö]';

    /**
     * Execute the stemming
     * @param  string $word The word to stem
     * @return string       The stemmed word
     */
    public function stem($word)
    {
        $word = rtrim($word);
        $word = strtolower($word);

        if (strlen($word) <= 2) {
            return $word;
        }

        /**
         * R1 is the region after the first non-vowel following a vowel, or is the
         * null region at the end of the word if there is no such non-vowel
         */
        if (preg_match('/' . $this->regexVowels . $this->regexNonVowels . '/u', $word, $matches, PREG_OFFSET_CAPTURE)) {
            $r1 = $matches[0][1] + 2;
        }

        /**
         * Do the actual stemming steps
         */
        $word = $this->step1($word, $r1);
        $word = $this->step2($word, $r1);
        $word = $this->step3($word, $r1);

        return $word;
    }

    /**
     * STEP 1:
     * Search and replace suffixes
     * @param  string $word The word to stem
     * @param  integer $r1  R1 position
     * @return string       Processed word
     */
    public function step1($word, $r1)
    {
        // Prepare word endings for matcing
        $endings = array(
            'a', 'arna', 'erna', 'heterna', 'orna', 'ad',
            'e', 'ade', 'ande', 'arne', 'are', 'aste', 'en',
            'anden', 'aren', 'heten', 'ern', 'ar', 'er', 'heter',
            'or', 'as', 'arnas', 'ernas', 'ornas', 'es', 'ades',
            'andes', 'ens', 'arens', 'hetens', 'erns', 'at', 'andet',
            'het', 'ast'
        );

        uasort($endings, function ($a, $b) {
            return strlen($a) - strlen($b);
        });

        $endings = array_reverse($endings);
        $regexEndings = '/(' . implode('|', $endings) . ')$/';

        // Match word endings and remove from word
        if ($r1) {
            $word = preg_replace($regexEndings, '', $word, 1);
        }

        // Delete valid "s"-ending of word
        $word = preg_replace('/([bcdfghjklmnoprtvy])s$/', '\\1', $word);

        return $word;
    }

    /**
     * STEP 2:
     * Search for suffixes in R1, and if found delete the last letter
     * @param  string $word The word to stem
     * @param  integer $r1  R1 position
     * @return string       Processed word
     */
    public function step2($word, $r1)
    {
        if (!$r1) {
            return $word;
        }

        if (preg_match('/(dd|gd|nn|dt|gt|kt|tt)$/', $word)) {
            $word = substr($word, 0, -1);
        }

        return $word;
    }

    public function step3($word, $r1)
    {
        if (!$r1) {
            return $word;
        }

        $word = preg_replace('/(lig|ig|els)$/', '', $word);
        $word = preg_replace('/löst$/', 'lös', $word);
        $word = preg_replace('/fullt$/', 'full', $word);

        return $word;
    }
}
