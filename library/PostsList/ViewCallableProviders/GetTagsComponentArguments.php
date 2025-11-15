<?php

namespace Municipio\PostsList\ViewCallableProviders;

use AcfService\Contracts\GetField;
use Municipio\Helper\Memoize\MemoizedFunction;
use Municipio\PostObject\PostObjectInterface;
use WP_Term;
use WpService\Contracts\GetTerms;

/*
 * View utility to get tags component arguments
 */
class GetTagsComponentArguments implements ViewCallableProviderInterface
{
    private MemoizedFunction $memoizedGetTerms;

    /**
     * Constructor
     *
     * @param PostObjectInterface[] $posts
     * @param string[] $taxonomies
     * @param GetTerms $wpService
     * @param GetField $acfService
     */
    public function __construct(
        private array $posts,
        private array $taxonomies,
        private GetTerms $wpService,
        private GetField $acfService
    ) {
    }

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (PostObjectInterface $post): array {
            $terms = $this->getAllPostsTerms($this->taxonomies);
            $terms = array_filter($terms, fn (WP_Term $term) => $term->object_id === $post->getId());
            return array_map(function (WP_Term $term) {
                return [
                    'label'    => $term->name,
                    'slug'     => $term->slug,
                    'taxonomy' => $term->taxonomy,
                    'color'    => $this->acfService->getField('colour', $term->taxonomy . '_' . $term->term_id)
                ];
            }, $terms);
        };
    }

    /**
     * Get all terms for the posts
     *
     * @param string[] $taxonomies
     * @return WP_Term[]
     */
    private function getAllPostsTerms(array $taxonomies): array
    {
        if (empty($taxonomies)) {
            return [];
        }

        if (!isset($this->memoizedGetTerms)) {
            $this->memoizedGetTerms = new MemoizedFunction(
                function (array $taxonomies, array $objectIds) {
                    return $this->wpService->getTerms([
                        'taxonomy'   => $taxonomies,
                        'object_ids' => $objectIds,
                        'hide_empty' => false,
                        'fields'     => 'all_with_object_id'
                    ]);
                }
            );
        }
        return ($this->memoizedGetTerms)($taxonomies, array_map(fn (PostObjectInterface $post) => $post->getId(), $this->posts));
    }
}
