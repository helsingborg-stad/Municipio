<?php

namespace Municipio\PostDecorators;

use WpService\Contracts\GetPostMeta;

/**
 * ApplySeoRedirect class.
 *
 * This class is a PostDecorator implementation that replaces the permalink to the redirect URL if a redirect is set.
 */
class ApplySeoRedirect implements PostDecorator
{
    /**
     * @param GetPostMeta $wpService The WordPress service for retrieving post meta.
     * @param PostDecorator|null $inner The inner post decorator. Defaults to a NullDecorator.
     */
    public function __construct(private GetPostMeta $wpService, private ?PostDecorator $inner = new NullDecorator())
    {
    }

    /**
     * Applies the SEO redirect to the post.
     *
     * @param \WP_Post $post The post to replace the url for.
     * @return \WP_Post The post with permalink replaced.
     */
    public function apply(\WP_Post $post): \WP_Post
    {
        $post = $this->inner->apply($post);

        $seoRedirectMetaUrl = $this->wpService->getPostMeta($post->ID, 'redirect', true);

        if (filter_var($seoRedirectMetaUrl, FILTER_VALIDATE_URL)) {
            $post->permalink = $seoRedirectMetaUrl;
        }

        return $post;
    }
}
