<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

/**
 * Feeds the design center with installed native font families.
 */
class DesignCenterFontFamilies implements Hookable
{
    public function __construct(
        private readonly WpService $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio/Styleguide/Customize/TokenData/FontFamilies', [$this, 'addStyleguideFontFamilies'], 10, 1);
    }

    /**
     * Adds installed native font families to styleguide options.
     *
     * @param array<int, array{value: string, label: string}> $options
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function addStyleguideFontFamilies(array $options): array
    {
        foreach ($this->getAvailableFontFamilies() as $fontFamily) {
            $options[] = [
                'value' => sprintf('"%s", sans-serif', $fontFamily),
                'label' => $fontFamily,
            ];
        }

        return array_values(array_reduce(
            $options,
            static function (array $uniqueOptions, array $option): array {
                if (!is_string($option['value'] ?? null) || !is_string($option['label'] ?? null)) {
                    return $uniqueOptions;
                }

                if ($option['value'] === '') {
                    return $uniqueOptions;
                }

                $uniqueOptions[$option['value']] = $option;

                return $uniqueOptions;
            },
            [],
        ));
    }

    /**
     * @return array<int, string>
     */
    private function getAvailableFontFamilies(): array
    {
        if (!$this->wpService->postTypeExists('wp_font_family')) {
            return [];
        }

        $posts = $this->wpService->getPosts([
            'post_type' => 'wp_font_family',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);

        return array_values(array_unique(array_filter(array_map(
            static fn(mixed $post): string => is_object($post) && property_exists($post, 'post_title') ? trim((string) $post->post_title) : '',
            $posts,
        ))));
    }
}
