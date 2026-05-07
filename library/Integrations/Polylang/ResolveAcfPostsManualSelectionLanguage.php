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
    private const MANUAL_POSTS_FIELD_NAME = 'posts_data_posts';
    private const MANUAL_POSTS_FIELD_KEY = 'field_571dfc6ff8115';

    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service.
     * @param ?Closure  $polylangIsActiveResolver Optional Polylang availability resolver.
     */
    public function __construct(
        private AddFilter $wpService,
        private ?Closure $polylangIsActiveResolver = null,
        private ?Closure $callStackResolver = null,
    ) {}

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        if (!$this->isPolylangActive()) {
            return;
        }

        // Register both field name and key hooks to support classic and block editor field contexts.
        $this->wpService->addFilter(
            sprintf('acf/fields/post_object/query/name=%s', self::MANUAL_POSTS_FIELD_NAME),
            [$this, 'makeManualPostsFieldLanguageAgnostic'],
            20,
            3,
        );

        $this->wpService->addFilter(
            sprintf('acf/fields/post_object/query/key=%s', self::MANUAL_POSTS_FIELD_KEY),
            [$this, 'makeManualPostsFieldLanguageAgnostic'],
            20,
            3,
        );

        // Keep selected post_object values visible on load in block editor for this field.
        $this->wpService->addFilter(
            'acf/acf_get_posts/args',
            [$this, 'makeManualPostsFieldLoadLanguageAgnostic'],
            20,
            1,
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
     * Makes loading selected posts for the manual posts field language agnostic.
     *
     * @param array<int|string, mixed> $args ACF get_posts arguments.
     *
     * @return array<int|string, mixed>
     */
    public function makeManualPostsFieldLoadLanguageAgnostic(array $args): array
    {
        if (!$this->isPolylangActive()) {
            return $args;
        }

        if (empty($args['post__in']) || !$this->isManualPostsFieldInCurrentCallStack()) {
            return $args;
        }

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
        return ($field['name'] ?? null) === self::MANUAL_POSTS_FIELD_NAME || ($field['key'] ?? null) === self::MANUAL_POSTS_FIELD_KEY;
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
     * Determines whether current call stack contains the manual posts post_object field context.
     *
     * @return bool
     */
    private function isManualPostsFieldInCurrentCallStack(): bool
    {
        foreach ($this->getCallStackResolver()?->__invoke() ?? [] as $frame) {
            if (!is_array($frame)) {
                continue;
            }

            $function = $frame['function'] ?? null;
            $args = $frame['args'] ?? [];

            if ($function !== 'get_posts' || !is_array($args) || !isset($args[1]) || !is_array($args[1])) {
                continue;
            }

            if ($this->isManualPostsField($args[1])) {
                return true;
            }
        }

        return false;
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

    /**
     * Gets the call stack resolver.
     *
     * @return Closure
     */
    private function getCallStackResolver(): Closure
    {
        if ($this->callStackResolver instanceof Closure) {
            return $this->callStackResolver;
        }

        return static fn(): array => debug_backtrace();
    }
}
