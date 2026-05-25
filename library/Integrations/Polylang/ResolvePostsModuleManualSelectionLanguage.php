<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Makes manual Posts module queries language agnostic when Polylang is active.
 */
class ResolvePostsModuleManualSelectionLanguage implements Hookable
{
    private const POSTS_MODULE_GET_POSTS_ARGS_FILTER = 'Modularity/Module/Posts/GetPosts/Args';
    private const MANUAL_DATA_SOURCE = 'manual';

    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service.
     * @param ?Closure  $polylangIsActiveResolver Optional Polylang availability resolver.
     */
    public function __construct(
        private AddFilter $wpService,
        private ?Closure $polylangIsActiveResolver = null,
    ) {}

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        if (!$this->isPolylangActive()) {
            return;
        }

        $this->wpService->addFilter(
            self::POSTS_MODULE_GET_POSTS_ARGS_FILTER,
            [$this, 'makeManualPostsQueryLanguageAgnostic'],
            20,
            4,
        );
    }

    /**
     * Makes manual Posts module query language agnostic.
     *
     * @param array<int|string, mixed> $args Query arguments.
     * @param array<int|string, mixed> $fields Module fields.
     * @param int $page Current page.
     * @param array<int, int|string> $stickyPostIds Sticky posts.
     *
     * @return array<int|string, mixed>
     */
    public function makeManualPostsQueryLanguageAgnostic(
        array $args,
        array $fields,
        int $page,
        array $stickyPostIds,
    ): array {
        if (!$this->isPolylangActive() || ($fields['posts_data_source'] ?? null) !== self::MANUAL_DATA_SOURCE) {
            return $args;
        }

        $args['lang'] = '';

        return $args;
    }

    /**
     * Determines whether Polylang is active.
     */
    private function isPolylangActive(): bool
    {
        return $this->getPolylangIsActiveResolver()?->__invoke() ?? false;
    }

    /**
     * Gets the Polylang availability resolver.
     */
    private function getPolylangIsActiveResolver(): ?Closure
    {
        if ($this->polylangIsActiveResolver instanceof Closure) {
            return $this->polylangIsActiveResolver;
        }

        if (!is_callable('pll_current_language')) {
            return null;
        }

        return static fn(): bool => true;
    }
}
