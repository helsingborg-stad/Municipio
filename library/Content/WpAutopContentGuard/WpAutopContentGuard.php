<?php

declare(strict_types=1);


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

        while (($wrappedSection = $this->locateWrappedSection($remaining, $openTag, $closeTag, $openLen, $closeLen)) !== null) {
            $result    .= $wrappedSection['before'] . $wrappedSection['content'];
            $remaining  = $wrappedSection['remaining'];
        }

        return $result . $remaining;
    }

    /**
     * Locates the next protected wrapper and returns the surrounding fragments.
     *
     * @param string $markup Markup to inspect.
     * @param string $openTag Protected opening tag.
     * @param string $closeTag Protected closing tag.
     * @param int $openLen Length of the protected opening tag.
     * @param int $closeLen Length of the protected closing tag.
     *
     * @return array{before: string, content: string, remaining: string}|null
     */
    private function locateWrappedSection(
        string $markup,
        string $openTag,
        string $closeTag,
        int $openLen,
        int $closeLen,
    ): ?array {
        $startPos = strpos($markup, $openTag);

        if ($startPos === false) {
            return null;
        }

        $before    = substr($markup, 0, $startPos);
        $afterOpen = substr($markup, $startPos + $openLen);
        $innerEndPos = $this->findMatchingClosingTag($afterOpen, $openTag, $closeTag, $openLen, $closeLen);

        if ($innerEndPos === null) {
            return [
                'before'    => $before,
                'content'   => $openTag . $afterOpen,
                'remaining' => '',
            ];
        }

        return [
            'before'    => $before,
            'content'   => substr($afterOpen, 0, $innerEndPos),
            'remaining' => substr($afterOpen, $innerEndPos + $closeLen),
        ];
    }

    /**
     * Finds the matching closing tag for a protected wrapper.
     *
     * @param string $markup Markup inside the outermost opening tag.
     * @param string $openTag Protected opening tag.
     * @param string $closeTag Protected closing tag.
     * @param int $openLen Length of the protected opening tag.
     * @param int $closeLen Length of the protected closing tag.
     *
     * @return int|null
     */
    private function findMatchingClosingTag(
        string $markup,
        string $openTag,
        string $closeTag,
        int $openLen,
        int $closeLen,
    ): ?int {
        $depth     = 1;
        $searchPos = 0;

        while ($searchPos < strlen($markup)) {
            $nextOpen  = strpos($markup, $openTag, $searchPos);
            $nextClose = strpos($markup, $closeTag, $searchPos);

            if ($nextClose === false) {
                return null;
            }

            if ($nextOpen !== false && $nextOpen < $nextClose) {
                $depth++;
                $searchPos = $nextOpen + $openLen;
                continue;
            }

            $depth--;

            if ($depth === 0) {
                return $nextClose;
            }

            $searchPos = $nextClose + $closeLen;
        }

        return null;
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