<?php

namespace Municipio\Content;

use Municipio\Content\HeadingStructure\Contracts\HeadingStructureDomAdapterInterface;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Normalizes heading levels in rendered HTML output.
 */
class HeadingStructure implements Hookable
{
    /**
     * @param AddFilter $wpService The WordPress filter service.
     * @param HeadingStructureDomAdapterInterface $domAdapter The DOM adapter used to inspect and rewrite headings.
     */
    public function __construct(
        private AddFilter $wpService,
        private HeadingStructureDomAdapterInterface $domAdapter,
    ) {
    }

    /**
     * Registers the HTML output filter.
     *
     * @return void
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter(
            'Website/HTML/output',
            [$this, 'correctHeadingStructure'],
            10,
            1,
        );
    }

    /**
     * Corrects the heading structure for the provided HTML.
     *
     * @param string $html The HTML to normalize.
     *
     * @return string The normalized HTML.
     */
    public function correctHeadingStructure(string $html): string
    {
        try {
            $this->domAdapter->load($html);
        } catch (\Throwable) {
            return $html;
        }

        $headingElements = $this->domAdapter->getHeadingElements();
        $context         = null;
        $hasSeenH1       = false;

        if (!$this->hasH1($headingElements)) {
            $candidate = $this->domAdapter->findAutoPromoteCandidate();
            if (is_object($candidate)) {
                $this->domAdapter->renameHeadingElement($candidate, 'h1');
                $headingElements = $this->domAdapter->getHeadingElements();
            }
        }

        foreach ($headingElements as $heading) {
            $tag          = $this->domAdapter->getTagName($heading);
            $correctedTag = $this->getCorrectHeadingLevel($tag, $context, $hasSeenH1);

            if ($correctedTag !== $tag) {
                $this->domAdapter->renameHeadingElement($heading, $correctedTag);
            }
        }

        return $this->domAdapter->saveHtml();
    }

    /**
     * Correct a heading level based on the current document context.
     * Prevents skipped levels (h2 → h4 becomes h2 → h3) and duplicate h1s.
     *
     * @param string $element The current heading tag.
     * @param int|null $context The current heading context.
     * @param bool $hasSeenH1 Whether a h1 has already been encountered.
     *
     * @return string The corrected heading tag.
     */
    private function getCorrectHeadingLevel(string $element, ?int &$context, bool &$hasSeenH1): string
    {
        $level = (int) substr($element, 1, 1);

        if ($context === null) {
            if ($element !== 'h1') {
                $context = 2;
                return 'h2';
            }
            $context   = 1;
            $hasSeenH1 = true;
            return $element;
        }

        if ($hasSeenH1 && $level === 1) {
            $context = 2;
            return 'h2';
        }

        if (($level - $context) > 1) {
            $context++;
            return 'h' . $context;
        }

        $context = $level;
        return $element;
    }

    /**
     * Determines whether the provided headings already contain a h1.
     *
     * @param array<object> $headingElements The heading elements in document order.
     *
     * @return bool True when a h1 exists, otherwise false.
     */
    private function hasH1(array $headingElements): bool
    {
        foreach ($headingElements as $heading) {
            if ($this->domAdapter->getTagName($heading) === 'h1') {
                return true;
            }
        }

        return false;
    }
}
