<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostTypes;

/**
 * Resolves page-for-post-type option values to the active Polylang language.
 */
class ResolveTranslatedPostTypeLink implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter&GetPostTypes $wpService The WordPress service.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     */
    public function __construct(
        private AddFilter&GetPostTypes $wpService,
        private ?Closure $translatedPostResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        foreach ($this->wpService->getPostTypes(['public' => true]) as $postType) {
            $this->wpService->addFilter(
                'option_page_for_' . $postType,
                [$this, 'resolveTranslatedPageId']
            );
        }
    }

    /**
     * Resolve the translated page ID for the active language.
     *
     * @param mixed $pageId The stored page ID.
     *
     * @return mixed
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
