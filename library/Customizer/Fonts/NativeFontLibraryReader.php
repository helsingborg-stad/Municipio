<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

/**
 * Reads installed fonts from WordPress' native font library.
 */
class NativeFontLibraryReader
{
    /**
     * @param NativeFontLibrarySupport $support
     */
    public function __construct(
        private readonly NativeFontLibrarySupport $support,
    ) {}

    /**
     * Returns installed native font family names.
     *
     * @return array<int, string>
     */
    public function getFontFamilies(): array
    {
        if (!$this->support->isAvailable() || !function_exists('get_posts')) {
            return [];
        }

        $posts = get_posts([
            'post_type'              => 'wp_font_family',
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'orderby'                => 'title',
            'order'                  => 'ASC',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);

        return array_values(array_unique(array_filter(array_map(
            static fn($post): string => is_object($post) && property_exists($post, 'post_title') ? (string) $post->post_title : '',
            $posts,
        ))));
    }
}
