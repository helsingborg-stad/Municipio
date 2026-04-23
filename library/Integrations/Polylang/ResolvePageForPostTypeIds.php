<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Resolves page-for-post-type IDs to the active Polylang language.
 */
class ResolvePageForPostTypeIds implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service.
     * @param ?Closure  $translatedPostResolver Optional translated post resolver.
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
        $this->wpService->addFilter('Municipio/Navigation/PageForPostTypeId', [$this, 'resolveTranslatedPageId'], 10, 2);
    }

    /**
     * Resolve the translated page-for-post-type ID when available.
     *
     * @param mixed  $pageId   The source page ID.
     * @param string $postType The post type using the mapping.
     *
     * @return mixed
     */
    public function resolveTranslatedPageId(mixed $pageId, string $postType): mixed
    {
        if (!is_numeric($pageId) || (int) $pageId <= 0 || $postType === '') {
            return $pageId;
        }

        $translatedPostResolver = $this->getTranslatedPostResolver();
        if ($translatedPostResolver === null) {
            return $pageId;
        }

        $translatedPageId = $translatedPostResolver((int) $pageId, $postType);

        return is_numeric($translatedPageId) && (int) $translatedPageId > 0
            ? (int) $translatedPageId
            : (int) $pageId;
    }

    /**
     * Get translated post resolver.
     *
     * @return ?Closure
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