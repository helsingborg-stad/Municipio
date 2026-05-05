<?php

declare(strict_types=1);

namespace Municipio\Integrations\Polylang;

use Closure;
use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Makes the Posts module manual selection query language agnostic in ACF.
 */
class ResolveAcfPostsManualSelectionLanguage implements Hookable
{
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
            'acf/fields/post_object/query/name=posts_data_posts',
            [$this, 'makeManualPostsFieldLanguageAgnostic'],
            20,
            3,
        );
    }

    /**
     * Makes the manual posts selection field language agnostic.
     *
     * @param array<int|string, mixed> $args Query arguments.
     * @param array<int|string, mixed> $field Field definition.
     * @param int|string $postId Post id context.
     *
     * @return array<int|string, mixed>
     */
    public function makeManualPostsFieldLanguageAgnostic(array $args, array $field, int|string $postId): array
    {
        if (!$this->isPolylangActive() || !$this->isManualPostsField($field)) {
            return $args;
        }

        // Empty language tells Polylang to include posts regardless of language assignment.
        $args['lang'] = '';

        return $args;
    }

    /**
     * Determines if the field is the Posts module manual post selector.
     *
     * @param array<int|string, mixed> $field Field definition.
     *
     * @return bool
     */
    private function isManualPostsField(array $field): bool
    {
        return ($field['name'] ?? null) === 'posts_data_posts';
    }

    /**
     * Determines whether Polylang is active.
     *
     * @return bool
     */
    private function isPolylangActive(): bool
    {
        return $this->getPolylangIsActiveResolver()?->__invoke() ?? false;
    }

    /**
     * Gets the Polylang availability resolver.
     *
     * @return ?Closure
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
