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

        if (empty($taxonomies)) {
            return $this->innerResolver->resolve();
        }

        $terms = $this->wpService->getTheTerms($this->postObject->getId(), $taxonomies[0]);

        if (empty($terms)) {
            return $this->innerResolver->resolve();
        }

        $icon = $this->termHelper->getTermIcon($terms[0]);

        if (empty($icon)) {
            return $this->innerResolver->resolve();
        }

        $color = $this->termHelper->getTermColor($terms[0]);

        return $this->getIconInstance($icon, $color ?? null);
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
