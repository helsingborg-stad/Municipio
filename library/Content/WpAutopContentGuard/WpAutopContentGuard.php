<?php

namespace Municipio\Content\WpAutopContentGuard;

/**
 * Protects markup from wpautop by wrapping it in a known sentinel element.
 */
class WpAutopContentGuard {
    private const string PROTECTED_CLASS = 'wpautop-protected';
    private const string PROTECTED_TAG = 'pre';

    /**
     * Wraps markup so wpautop skips it.
     *
     * @param string $markup Markup to protect.
     *
     * @return string
     */
    public function lock(string $markup): string
    {
        return $this->getProtectedOpeningTag() . $markup . $this->getProtectedClosingTag();
    }

    /**
     * Removes every protective wrapper, including nested wrappers.
     *
     * @param string $markup Markup that may contain protected sections.
     *
     * @return string
     */
    public function unlock(string $markup): string
    {
        if (!str_contains($markup, $this->getProtectedOpeningTag())) {
            return $markup;
        }

        return $this->unwrapProtectedMarkup($markup);
    }

    /**
     * Repeatedly unwraps one nesting level at a time until no wrappers remain.
     *
     * @param string $markup Protected markup.
     *
     * @return string
     */
    private function unwrapProtectedMarkup(string $markup): string
    {
        $unwrappedMarkup = $markup;

        do {
            $previousMarkup = $unwrappedMarkup;
            $unwrappedMarkup = preg_replace($this->getProtectedWrapperPattern(), '$1', $unwrappedMarkup) ?? $previousMarkup;
        } while ($unwrappedMarkup !== $previousMarkup);

        return $unwrappedMarkup;
    }

    /**
     * Returns the exact opening tag used to protect content.
     *
     * @return string
     */
    private function getProtectedOpeningTag(): string
    {
        return '<' . self::PROTECTED_TAG . ' class="' . self::PROTECTED_CLASS . '">';
    }

    /**
     * Returns the exact closing tag used to protect content.
     *
     * @return string
     */
    private function getProtectedClosingTag(): string
    {
        return '</' . self::PROTECTED_TAG . '>';
    }

    /**
     * Matches the innermost protected wrapper so nested sections can be released safely.
     *
     * @return string
     */
    private function getProtectedWrapperPattern(): string
    {
        return '#'
            . preg_quote($this->getProtectedOpeningTag(), '#')
            . '((?:(?!'
            . preg_quote($this->getProtectedOpeningTag(), '#')
            . ').)*)'
            . preg_quote($this->getProtectedClosingTag(), '#')
            . '#is';
    }

}