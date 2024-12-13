<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\TermIcon\TryGetTermIconInterface;
use Municipio\PostObject\PostObjectInterface;
use WpService\Contracts\GetObjectTaxonomies;
use WpService\Contracts\GetTheTerms;

/**
 * Post object decorator that adds term icons to the post object.
 */
class PostObjectWithTermIcons extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * @var \Municipio\PostObject\TermIcon\TermIconInterface[]|null
     */
    protected ?array $termIconsCache = null;

    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private GetObjectTaxonomies&GetTheTerms $wpService,
        private TryGetTermIconInterface $tryGetTermIcon
    ) {
        $this->postObject = $postObject;
    }

    /**
     * @inheritDoc
     */
    public function getTermIcons(): array
    {
        if ($this->termIconsCache !== null) {
            return $this->termIconsCache;
        }

        $this->termIconsCache = [];

        $taxonomies = $this->wpService->getObjectTaxonomies($this->postObject->getPostType());

        foreach ($taxonomies as $taxonomy) {
            $terms = $this->wpService->getTheTerms($this->postObject->getId(), $taxonomy);

            if (empty($terms)) {
                continue;
            }

            foreach ($terms as $term) {
                $termIcon = $this->tryGetTermIcon->tryGetTermIcon($term->term_id, $taxonomy);

                if ($termIcon !== null) {
                    $this->termIconsCache[] = $termIcon;
                }
            }
        }

        return $this->termIconsCache;
    }
}
