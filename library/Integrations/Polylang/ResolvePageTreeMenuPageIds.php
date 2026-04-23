<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Resolves language-specific page IDs used by page tree menus.
 */
class ResolvePageTreeMenuPageIds implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     */
    public function __construct(
        private AddFilter $wpService,
        private ?Closure $translatedPostResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('option_page_on_front', [$this, 'resolveTranslatedPageId']);
        $this->wpService->addFilter('option_page_for_posts', [$this, 'resolveTranslatedPageId']);
    }

    /**
     * Resolve a translated page ID for the active language when possible.
     *
     * @param mixed $pageId The original page ID.
     *
     * @return mixed The translated page ID, or the original value.
     */
    public function resolveTranslatedPageId(mixed $pageId): mixed
    {
        if (!is_numeric($pageId) || (int) $pageId <= 0) {
            return $pageId;
        }

        $translatedPostResolver = $this->getTranslatedPostResolver();

        if ($translatedPostResolver === null) {
            return $pageId;
        }

        $translatedPageId = $translatedPostResolver((int) $pageId);

        return is_numeric($translatedPageId) && (int) $translatedPageId > 0
            ? (int) $translatedPageId
            : (int) $pageId;
    }

    /**
     * Get the translated post resolver.
     *
     * @return ?Closure The translated post resolver.
     */
    private function getTranslatedPostResolver(): ?Closure
    {
        if ($this->translatedPostResolver instanceof Closure) {
            return $this->translatedPostResolver;
        }

        if (!is_callable('pll_get_post')) {
            return null;
        }

        return static fn (int $pageId): mixed => call_user_func('pll_get_post', $pageId);
    }
}
