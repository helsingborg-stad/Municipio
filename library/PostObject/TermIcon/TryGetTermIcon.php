<?php

namespace Municipio\PostObject\TermIcon;

use Municipio\PostObject\TermIcon\TermIconInterface;

/**
 * Tries to get the term icon for a given term ID and taxonomy.
 */
class TryGetTermIcon implements TryGetTermIconInterface
{
    /**
     * @inheritDoc
     */
    public function tryGetTermIcon(int $termId, string $taxonomy): ?TermIconInterface
    {
        $termHelper = new \Municipio\Helper\Term\Term(
            \Municipio\Helper\WpService::get(),
            \Municipio\Helper\AcfService::get()
        );

        $icon  = $termHelper->getTermIcon($termId, $taxonomy);
        $color = $termHelper->getTermColor($termId, $taxonomy);

        if (!empty($icon) && !empty($icon['src']) && $icon['type'] == 'icon') {
            return $this->createTermIcon($termId, $icon['src'], $color);
        }

        return null;
    }

    /**
     * Create a term icon from an array.
     *
     * @param array $data
     * @return \Municipio\PostObject\TermIcon\TermIconInterface
     */
    private function createTermIcon(int $termId, string $icon, string $color): TermIconInterface
    {
        return new class ($termId, $icon, $color) implements TermIconInterface {
            /**
             * Constructor.
             */
            public function __construct(private int $termId, private string $icon, private string $color)
            {
            }

            /**
             * @inheritDoc
             */
            public function getIcon(): string
            {
                return $this->icon;
            }

            /**
             * @inheritDoc
             */
            public function getColor(): string
            {
                return $this->color;
            }

            /**
             * @inheritDoc
             */
            public function getTermId(): int
            {
                return $this->termId;
            }
        };
    }
}
