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
        $openTag = $this->getProtectedOpeningTag();

        if (!str_contains($markup, $openTag)) {
            return $markup;
        }

        do {
            $previous = $markup;
            $markup   = $this->unwrapProtectedMarkup($markup);
        } while ($markup !== $previous && str_contains($markup, $openTag));

        return $markup;
    }

    /**
     * Walks the markup string and strips every protected wrapper while preserving
     * inner content. Uses a depth counter to correctly handle nested wrappers
     * without relying on PCRE, which fails with a backtrack-limit error on large
     * full-page HTML documents.
     *
     * @param string $markup Protected markup.
     *
     * @return string
     */
    private function unwrapProtectedMarkup(string $markup): string
    {
        $openTag   = $this->getProtectedOpeningTag();
        $closeTag  = $this->getProtectedClosingTag();
        $openLen   = strlen($openTag);
        $closeLen  = strlen($closeTag);
        $result    = '';
        $remaining = $markup;

        while (($startPos = strpos($remaining, $openTag)) !== false) {
            $result   .= substr($remaining, 0, $startPos);
            $afterOpen = substr($remaining, $startPos + $openLen);

            $depth       = 1;
            $searchPos   = 0;
            $innerEndPos = null;

            while ($searchPos < strlen($afterOpen)) {
                $nextOpen  = strpos($afterOpen, $openTag, $searchPos);
                $nextClose = strpos($afterOpen, $closeTag, $searchPos);

                if ($nextClose === false) {
                    // Malformed markup: no matching closing tag; keep wrapper as-is.
                    $result   .= $openTag . $afterOpen;
                    $remaining = '';
                    break 2;
                }

                if ($nextOpen !== false && $nextOpen < $nextClose) {
                    $depth++;
                    $searchPos = $nextOpen + $openLen;
                } else {
                    $depth--;
                    if ($depth === 0) {
                        $innerEndPos = $nextClose;
                        break;
                    }
                    $searchPos = $nextClose + $closeLen;
                }
            }

            if ($innerEndPos !== null) {
                $result   .= substr($afterOpen, 0, $innerEndPos);
                $remaining = substr($afterOpen, $innerEndPos + $closeLen);
            }
        }

        return $result . $remaining;
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

}