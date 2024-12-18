<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\TermIcon\TryGetTermIconInterface;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\TermIcon\TermIconInterface;
use WpService\Contracts\GetObjectTaxonomies;
use WpService\Contracts\GetTaxonomy;
use WpService\Contracts\GetTerm;
use WpService\Contracts\GetTheTerms;

/**
 * Post object decorator that adds term icons to the post object.
 */
class PostObjectWithTermIcons extends AbstractPostObjectDecorator implements PostObjectInterface
{
    /**
     * @var \Municipio\PostObject\TermIcon\TermIconInterface[]|null
     */
    protected ?array $termIconsCache    = null;
    protected static array $cachedTerms = [];

    /**
     * Constructor.
     */
    public function __construct(
        PostObjectInterface $postObject,
        private GetObjectTaxonomies&GetTheTerms&GetTerm $wpService,
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

    /**
     * @inheritDoc
     */
    public function getTermIcon(?string $taxonomy = null): ?TermIconInterface
    {
        $termIcons = $this->getTermIcons();

        if ($taxonomy === null) {
            return $termIcons[0] ?? null;
        }

        foreach ($termIcons as $termIcon) {
            $termId = $termIcon->getTermId();

            if (!isset(self::$cachedTerms[$termId])) {
                self::$cachedTerms[$termId] = $this->wpService->getTerm($termId, '', 'OBJECT');
            }

            if (self::$cachedTerms[$termId]->taxonomy === $taxonomy) {
                return $termIcon;
            }
        }

        return null;
    }
}
