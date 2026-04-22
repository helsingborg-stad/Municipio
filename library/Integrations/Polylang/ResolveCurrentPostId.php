<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Resolves current page IDs for localized post type archives.
 */
class ResolveCurrentPostId implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service.
     * @param ?Closure $isPostTypeArchiveResolver Optional archive resolver.
     * @param ?Closure $currentPostTypeResolver Optional current post type resolver.
     * @param ?Closure $archivePageIdResolver Optional page-for-post-type resolver.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     * @param ?Closure $currentArchivePageIdResolver Optional localized archive page ID resolver.
     */
    public function __construct(
        private AddFilter $wpService,
        private ?Closure $isPostTypeArchiveResolver = null,
        private ?Closure $currentPostTypeResolver = null,
        private ?Closure $archivePageIdResolver = null,
        private ?Closure $translatedPostResolver = null,
        private ?Closure $currentArchivePageIdResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/Helper/CurrentPostId', [$this, 'resolveCurrentPostId']);
    }

    /**
     * Resolve the current page ID for a localized archive page.
     *
     * @param mixed $currentPostId The resolved current page ID.
     *
     * @return mixed
     */
    public function resolveCurrentPostId(mixed $currentPostId): mixed
    {
        if (!($this->getIsPostTypeArchiveResolver())()) {
            return $currentPostId;
        }

        $postType = ($this->getCurrentPostTypeResolver())();
        if (!is_string($postType) || $postType === '') {
            return $currentPostId;
        }

        $archivePageId = ($this->getArchivePageIdResolver())($postType);
        if (is_numeric($archivePageId) && (int) $archivePageId > 0) {
            $translatedPostResolver = $this->getTranslatedPostResolver();
            if ($translatedPostResolver instanceof Closure) {
                $translatedArchivePageId = $translatedPostResolver((int) $archivePageId);

                if (is_numeric($translatedArchivePageId) && (int) $translatedArchivePageId > 0) {
                    return (int) $translatedArchivePageId;
                }
            }

            return (int) $archivePageId;
        }

        $currentArchivePageId = ($this->getCurrentArchivePageIdResolver())();

        return is_numeric($currentArchivePageId) && (int) $currentArchivePageId > 0
            ? (int) $currentArchivePageId
            : $currentPostId;
    }

    /**
     * Get the archive resolver.
     *
     * @return Closure
     */
    private function getIsPostTypeArchiveResolver(): Closure
    {
        if ($this->isPostTypeArchiveResolver instanceof Closure) {
            return $this->isPostTypeArchiveResolver;
        }

        return static fn (): bool => is_post_type_archive();
    }

    /**
     * Get the current post type resolver.
     *
     * @return Closure
     */
    private function getCurrentPostTypeResolver(): Closure
    {
        if ($this->currentPostTypeResolver instanceof Closure) {
            return $this->currentPostTypeResolver;
        }

        return static function (): ?string {
            $postType = get_post_type();
            if (is_string($postType) && $postType !== '') {
                return $postType;
            }

            $queriedObject = get_queried_object();

            return is_object($queriedObject) && isset($queriedObject->name) && is_string($queriedObject->name)
                ? $queriedObject->name
                : null;
        };
    }

    /**
     * Get the archive page ID resolver.
     *
     * @return Closure
     */
    private function getArchivePageIdResolver(): Closure
    {
        if ($this->archivePageIdResolver instanceof Closure) {
            return $this->archivePageIdResolver;
        }

        return static fn (string $postType): mixed => get_option('page_for_' . $postType);
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

        return static fn (int $postId): mixed => call_user_func('pll_get_post', $postId);
    }

    /**
     * Get the localized archive page ID resolver.
     *
     * @return Closure
     */
    private function getCurrentArchivePageIdResolver(): Closure
    {
        if ($this->currentArchivePageIdResolver instanceof Closure) {
            return $this->currentArchivePageIdResolver;
        }

        return static function (): int {
            global $wp;

            if (!isset($wp->request) || !is_string($wp->request) || $wp->request === '') {
                return 0;
            }

            return (int) url_to_postid(home_url('/' . ltrim($wp->request, '/')));
        };
    }
}