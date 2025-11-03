<?php

namespace Municipio\PostsList\ViewUtilities;

use Municipio\Helper\Memoize\MemoizedFunction;
use Municipio\PostObject\PostObjectInterface;
use WP_Term;
use WpService\Contracts\GetTerms;

/*
 * View utility to get terms as string
 */
class GetTermsAsString implements ViewUtilityInterface
{
    private MemoizedFunction $memoizedGetTerms;

    /**
     * Constructor
     *
     * @param PostObjectInterface[] $posts
     * @param string[] $taxonomies
     * @param GetTerms $wpService
     */
    public function __construct(
        private array $posts,
        private array $taxonomies,
        private GetTerms $wpService,
        private string $separator = ', '
    ) {
    }

    /**
     * Get the callable for the view utility
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return function (PostObjectInterface $post): string {
            $terms = $this->getAllPostsTerms($this->taxonomies);
            $terms = array_filter($terms, fn (WP_Term $term) => $term->object_id === $post->getId());

            return implode($this->separator, array_map(function (WP_Term $term) {
                return $term->name;
            }, $terms));
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
