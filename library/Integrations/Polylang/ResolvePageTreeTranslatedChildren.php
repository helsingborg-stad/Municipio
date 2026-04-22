<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Helper\CurrentPostId;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetPostType;
use WpService\Contracts\GetTheTitle;

/**
 * Resolves translated page tree children when translated pages do not mirror parent IDs.
 */
class ResolvePageTreeTranslatedChildren implements Hookable
{
    /**
     * Constructor.
     *
     * @param AddFilter&GetPostType&GetTheTitle $wpService The WordPress service.
     * @param ?Closure $postTranslationsResolver Optional post translations resolver.
     * @param ?Closure $currentLanguageResolver Optional current language resolver.
     * @param ?Closure $translatedPostResolver Optional translated post resolver.
     * @param ?Closure $defaultLanguageResolver Optional default language resolver.
     * @param ?Closure $childrenByParentResolver Optional children resolver.
     * @param ?Closure $currentPostIdResolver Optional current post ID resolver.
     * @param ?Closure $ancestorIdsResolver Optional ancestor IDs resolver.
     */
    public function __construct(
        private AddFilter&GetPostType&GetTheTitle $wpService,
        private ?Closure $postTranslationsResolver = null,
        private ?Closure $currentLanguageResolver = null,
        private ?Closure $translatedPostResolver = null,
        private ?Closure $defaultLanguageResolver = null,
        private ?Closure $childrenByParentResolver = null,
        private ?Closure $currentPostIdResolver = null,
        private ?Closure $ancestorIdsResolver = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter(
            'Municipio/Navigation/PageTree/Children',
            [$this, 'resolveTranslatedChildren'],
            10,
            2
        );
    }

    /**
     * Resolve translated children for the provided page ID.
     *
     * @param array $children Existing children.
     * @param int   $postId   Current page ID.
     *
     * @return array Translated children or existing children.
     */
    public function resolveTranslatedChildren(array $children, int $postId): array
    {
        if (!empty($children) || $postId <= 0) {
            return $children;
        }

        $postTranslationsResolver = $this->getPostTranslationsResolver();
        $currentLanguageResolver  = $this->getCurrentLanguageResolver();
        $translatedPostResolver   = $this->getTranslatedPostResolver();

        if (
            $postTranslationsResolver === null ||
            $currentLanguageResolver === null ||
            $translatedPostResolver === null
        ) {
            return $children;
        }

        $translations = $postTranslationsResolver($postId);
        $currentLang  = $currentLanguageResolver();

        if (!is_array($translations) || !is_string($currentLang) || $currentLang === '') {
            return $children;
        }

        $sourcePostId = $this->resolveSourcePostId($translations, $currentLang);
        if ($sourcePostId === null) {
            return $children;
        }

        $sourceChildren = $this->getChildrenByParentResolver()?->__invoke(
            $sourcePostId,
            (string) $this->wpService->getPostType($sourcePostId)
        );

        if (!is_array($sourceChildren) || empty($sourceChildren)) {
            return $children;
        }

        $translatedChildren = [];
        $currentPostId      = $this->getCurrentPostIdResolver()?->__invoke();
        $ancestorIds        = $this->getAncestorIdsResolver()?->__invoke();
        $ancestorIds        = is_array($ancestorIds) ? $ancestorIds : [];

        foreach ($sourceChildren as $sourceChild) {
            if (empty($sourceChild['ID']) || !is_numeric($sourceChild['ID'])) {
                continue;
            }

            $translatedChildId = $translatedPostResolver((int) $sourceChild['ID'], $currentLang);

            if (!is_numeric($translatedChildId) || (int) $translatedChildId <= 0) {
                continue;
            }

            $translatedChildId = (int) $translatedChildId;

            $translatedChildren[] = [
                'ID'          => $translatedChildId,
                'post_title'  => (string) $this->wpService->getTheTitle($translatedChildId),
                'post_parent' => $postId,
                'post_type'   => (string) $this->wpService->getPostType($translatedChildId),
                'active'      => is_numeric($currentPostId) && (int) $currentPostId === $translatedChildId,
                'ancestor'    => in_array($translatedChildId, $ancestorIds),
            ];
        }

        return !empty($translatedChildren) ? $translatedChildren : $children;
    }

    /**
     * Resolve source language page ID from translations.
     *
     * @param array<string, mixed> $translations Translations keyed by language slug.
     * @param string               $currentLang  Current language slug.
     *
     * @return int|null The selected source page ID.
     */
    private function resolveSourcePostId(array $translations, string $currentLang): ?int
    {
        $defaultLanguage = $this->getDefaultLanguageResolver()?->__invoke();

        if (
            is_string($defaultLanguage) &&
            $defaultLanguage !== '' &&
            $defaultLanguage !== $currentLang &&
            isset($translations[$defaultLanguage]) &&
            is_numeric($translations[$defaultLanguage]) &&
            (int) $translations[$defaultLanguage] > 0
        ) {
            return (int) $translations[$defaultLanguage];
        }

        foreach ($translations as $lang => $translatedPostId) {
            if ($lang === $currentLang || !is_numeric($translatedPostId) || (int) $translatedPostId <= 0) {
                continue;
            }

            return (int) $translatedPostId;
        }

        return null;
    }

    /**
     * Get the post translations resolver.
     *
     * @return ?Closure The post translations resolver.
     */
    private function getPostTranslationsResolver(): ?Closure
    {
        if ($this->postTranslationsResolver instanceof Closure) {
            return $this->postTranslationsResolver;
        }

        if (!is_callable('pll_get_post_translations')) {
            return null;
        }

        return static fn (int $postId): mixed => call_user_func('pll_get_post_translations', $postId);
    }

    /**
     * Get the current language resolver.
     *
     * @return ?Closure The current language resolver.
     */
    private function getCurrentLanguageResolver(): ?Closure
    {
        if ($this->currentLanguageResolver instanceof Closure) {
            return $this->currentLanguageResolver;
        }

        return static function (): ?string {
            if (is_callable('pll_current_language')) {
                $language = call_user_func('pll_current_language', 'slug');

                if (is_string($language) && $language !== '') {
                    return $language;
                }
            }

            $requestLanguage = $_GET['lang'] ?? null;

            return is_string($requestLanguage) && $requestLanguage !== '' ? $requestLanguage : null;
        };
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

        return static fn (int $postId, string $language): mixed => call_user_func('pll_get_post', $postId, $language);
    }

    /**
     * Get the default language resolver.
     *
     * @return ?Closure The default language resolver.
     */
    private function getDefaultLanguageResolver(): ?Closure
    {
        if ($this->defaultLanguageResolver instanceof Closure) {
            return $this->defaultLanguageResolver;
        }

        if (!is_callable('pll_default_language')) {
            return null;
        }

        return static fn (): mixed => call_user_func('pll_default_language', 'slug');
    }

    /**
     * Get the children resolver.
     *
     * @return ?Closure The children resolver.
     */
    private function getChildrenByParentResolver(): ?Closure
    {
        if ($this->childrenByParentResolver instanceof Closure) {
            return $this->childrenByParentResolver;
        }

        return static fn (int $postId, string $postType): array =>
            GetPostsByParent::getPostsByParent($postId, $postType);
    }

    /**
     * Get the current post ID resolver.
     *
     * @return ?Closure The current post ID resolver.
     */
    private function getCurrentPostIdResolver(): ?Closure
    {
        if ($this->currentPostIdResolver instanceof Closure) {
            return $this->currentPostIdResolver;
        }

        return static fn (): int => (int) CurrentPostId::get();
    }

    /**
     * Get the ancestor IDs resolver.
     *
     * @return ?Closure The ancestor IDs resolver.
     */
    private function getAncestorIdsResolver(): ?Closure
    {
        if ($this->ancestorIdsResolver instanceof Closure) {
            return $this->ancestorIdsResolver;
        }

        return static fn (): array => GetAncestors::getAncestors();
    }
}
