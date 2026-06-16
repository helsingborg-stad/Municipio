<?php

namespace Municipio\Content\WpAutopContentGuard;

class WpAutopContentGuard {
    private const string PROTECTED_CLASS = 'wpautop-protected';
    private const string PROTECTED_TAG = 'pre';

    public function lock(string $markup): string
    {
        return '<' . self::PROTECTED_TAG . ' class="' . self::PROTECTED_CLASS . '">' . $markup . '</' . self::PROTECTED_TAG . '>';
    }

    public function unlock(string $markup): string
    {
        $pattern        = '#<' . self::PROTECTED_TAG . ' class="' . self::PROTECTED_CLASS . '">((?:(?!<'
            . self::PROTECTED_TAG . ' class="' . self::PROTECTED_CLASS . '">).)*)</' . self::PROTECTED_TAG . '>#is';
        $unlockedMarkup = $markup;

        do {
            $previousMarkup = $unlockedMarkup;
            $unlockedMarkup = preg_replace($pattern, '$1', $unlockedMarkup) ?? $previousMarkup;
        } while ($unlockedMarkup !== $previousMarkup);

        return $unlockedMarkup;
    }
}