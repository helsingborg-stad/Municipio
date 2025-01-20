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
    private static array $termIcons  = [];
    private static array $termColors = [];

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

        [$icon, $iconColor] = $this->getIconFromTerm($taxonomies);

        if ($icon) {
            return $this->getIconInstance($icon, $iconColor);
        }

        return  $this->innerResolver->resolve();
    }

    /**
     * Retrieves the icon and color associated with a term from the given taxonomies.
     *
     * @param array $taxonomies An array of taxonomies to search for terms.
     * @return array An array containing the icon and color associated with the term, or [null, null] if no matching term is found.
     */
    private function getIconFromTerm(array $taxonomies)
    {
        foreach ($taxonomies as $taxonomy) {
            $terms = $this->wpService->getTheTerms($this->postObject->getId(), $taxonomy);

            if (empty($terms)) {
                continue;
            }

            foreach ($terms as $term) {
                if (in_array($term->term_id, self::$termIcons)) {
                    if (empty(self::$termsIcons[$term->term_id])) {
                        continue;
                    }

                    return [self::$termIcons[$term->term_id], self::$termColors[$term->term_id] ?? null];
                }

                self::$termIcons[$term->term_id]  = $this->termHelper->getTermIcon($term);
                self::$termColors[$term->term_id] = !empty(self::$termIcons[$term->term_id]) ? $this->termHelper->getTermColor($term) : false;

                if (self::$termIcons[$term->term_id]) {
                    return [self::$termIcons[$term->term_id], self::$termColors[$term->term_id] ?? null];
                }
            }
        }

        return [null, null];
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
