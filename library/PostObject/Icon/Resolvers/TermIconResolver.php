<?php

namespace Municipio\PostObject\Icon\Resolvers;

use Municipio\Helper\Term\Contracts\{GetTermColor, GetTermIcon};
use Municipio\PostObject\{PostObjectInterface, Icon\Icon, Icon\IconInterface};
use WpService\Contracts\{GetObjectTaxonomies, GetTheTerms};

/**
 * Term icon resolver.
 */
class TermIconResolver implements IconResolverInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PostObjectInterface $postObject,
        private GetObjectTaxonomies&GetTheTerms $wpService,
        private GetTermIcon&GetTermColor $termHelper,
        private IconResolverInterface $innerResolver
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(): ?IconInterface
    {
        $taxonomies = $this->wpService->getObjectTaxonomies($this->postObject->getPostType());

        return $this->getTermIconFromTaxonomies($taxonomies) ?? $this->innerResolver->resolve();
    }

    /**
     * Get term icon from taxonomies.
     *
     * @param string[] $taxonomies taxonomy names
     * @return IconInterface|null
     */
    private function getTermIconFromTaxonomies(array $taxonomies): ?IconInterface
    {
        if (empty($taxonomies)) {
            return null;
        }

        foreach ($taxonomies as $taxonomy) {
            $terms = $this->wpService->getTheTerms($this->postObject->getId(), $taxonomy);

            if (!is_array($terms) || empty($terms)) {
                continue;
            }

            if (!is_null($termIcon = $this->getTermIconFromTerms($terms))) {
                return $termIcon;
            }
        }

        return null;
    }

    /**
     * Get term icon from terms.
     *
     * @param WP_Term[] $terms
     * @return IconInterface|null
     */
    private function getTermIconFromTerms(array $terms): ?IconInterface
    {
        foreach ($terms as $term) {
            $icon = $this->termHelper->getTermIcon($term);

            if (!is_array($icon) || empty($icon)) {
                continue;
            }

            $color = $this->termHelper->getTermColor($term);

            return $this->getIconInstance($icon, $color);
        }

        return null;
    }


    /**
     * Get icon instance.
     *
     * @param array $icon
     * @param string|null $color
     *
     * @return IconInterface
     */
    private function getIconInstance(array $icon, ?string $color = null): IconInterface
    {
        return new class ($icon, $color) extends Icon implements IconInterface {
            /**
             * Constructor.
             */
            public function __construct(private array $icon, private ?string $color)
            {
            }

            /**
             * @inheritDoc
             */
            public function getIcon(): string
            {
                return $this->icon['src'];
            }

            /**
             * @inheritDoc
             */
            public function getCustomColor(): string
            {
                return $this->color ?? '';
            }
        };
    }
}
