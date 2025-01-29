<?php

namespace Municipio\PostDecorators;

/**
 * ApplyProjectTerms class.
 *
 * This class is responsible for applying project terms as a post decorator.
 * It implements the PostDecorator interface.
 *
 */
class ApplyProjectTerms implements PostDecorator
{
    /**
     * @param PostDecorator|null $inner The inner post decorator. Defaults to a new instance of NullDecorator.
     */
    public function __construct(private ?PostDecorator $inner = new NullDecorator())
    {
    }

    /**
     * Applies project terms to a given post.
     *
     * @param \WP_Post $post The post to apply the terms to.
     * @return \WP_Post The post with the applied terms.
     */
    public function apply(\WP_Post $post): \WP_Post
    {
        $post = $this->inner->apply($post);

        if (empty($post->schemaObject) || $post->schemaObject->getType() !== 'Project') {
            return $post;
        }


        return $this->setTerms($post);
    }

    /**
     * Sets the terms for a given post.
     *
     * This method sets the terms for a given post by mapping the terms from the 'termsUnlinked' property
     * to the corresponding taxonomies defined in the 'map' array. It skips any terms that do not have a
     * taxonomy or a label. The resulting terms are stored in the 'projectTerms' property of the post object.
     *
     * @param \WP_Post $post The post object to set the terms for.
     * @return \WP_Post The post object with the terms set.
     */
    private function setTerms(\WP_Post $post): \WP_Post
    {
        if (empty($post->termsUnlinked)) {
            return $post;
        }

        $map = [
            'project_meta_category'   => 'category',
            'project_meta_technology' => 'technology',
            'project_meta_status'     => 'status',
            'project_department'      => 'department'
        ];

        $terms = [];

        foreach ($post->termsUnlinked as $term) {
            if (empty($term['taxonomy'])) {
                continue;
            }

            if (isset($map[$term['taxonomy']]) && !empty($term['label'])) {
                $terms[$map[$term['taxonomy']]][] = $term['label'];
            }
        }

        $post->projectTerms = $terms;

        return $post;
    }
}
