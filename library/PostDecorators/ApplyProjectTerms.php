<?php

namespace Municipio\PostDecorators;

class ApplyProjectTerms implements PostDecorator
{
    public function __construct(private ?PostDecorator $inner = new NullDecorator())
    {
    }

    public function apply(\WP_Post $post): \WP_Post
    {
        $post = $this->inner->apply($post);

        if (empty($post->schemaObject) || $post->schemaObject->getType() !== 'Project') {
            return $post;
        }


        return $this->setTerms($post);
    }

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
