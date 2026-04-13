<?php

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostTypes;

/**
 * Resolves language-specific page IDs used by page tree menus.
 */
class ResolvePageTreeMenuPageIds implements Hookable
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
        foreach ($this->getOptionHooks() as $hookName) {
            $this->wpService->addFilter($hookName, [$this, 'resolveTranslatedPageId']);
        }
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
     * Get the option hooks that should resolve translated page IDs.
     *
     * @return array<string> The option hooks to register.
     */
    private function getOptionHooks(): array
    {
        $hooks = [
            'option_page_on_front',
            'option_page_for_posts',
        ];

        foreach ($this->wpService->getPostTypes(['public' => true, 'hierarchical' => true]) as $postType) {
            $hooks[] = sprintf('option_page_for_%s', $postType);
        }

        return array_values(array_unique($hooks));
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
