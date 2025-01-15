<?php

namespace Municipio\Helper\Term;

use AcfService\Contracts\GetField;
use Municipio\Helper\Term\Contracts\CreateOrGetTermIdFromString;
use Municipio\Helper\Term\Contracts\GetTermColor;
use Municipio\Helper\Term\Contracts\GetTermIcon;
use WP_Term;
use WpService\Contracts\{WpInsertTerm, WpGetAttachmentImageUrl, IsWpError, GetTermBy, GetAncestors, ApplyFilters};

/**
 * Class Term
 */
class Term implements GetTermColor, GetTermIcon, CreateOrGetTermIdFromString
{
    /**
     * Constructor.
     */
    public function __construct(
        private GetTermBy&ApplyFilters&GetAncestors&WpGetAttachmentImageUrl&WpInsertTerm&IsWpError $wpService,
        private GetField $acfService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getTermColor(int|string|\WP_Term $term, string $taxonomy = ''): false|string
    {
        if (empty($term)) {
            return false;
        }

        $term = $this->getTerm($term, $taxonomy);

        if (empty($term)) {
            return false;
        }

        $color = $this->acfService->getField('colour', 'term_' . $term->term_id);

        if (is_string($color) && "" !== $color && !str_starts_with($color, '#')) {
            $color = "#{$color}";
        } elseif ("" === $color || !$color) {
            $color = $this->getAncestorTermColor($term);
        }

        return $this->wpService->applyFilters('Municipio/getTermColour', $color, $term, $taxonomy);
    }

    /**
     * Gets term color from ancestor term
     * @param WP_Term $term The term to get the color for. Can be a term object, term ID or term slug.
     *
     * @return string|false
     */
    public function getAncestorTermColor(\WP_Term $term): string|false
    {
        $ancestors = $this->wpService->getAncestors($term->term_id, $term->taxonomy, 'taxonomy');
        if (!empty($ancestors)) {
            foreach ($ancestors as $ancestorId) {
                $color = $this->acfService->getField('colour', 'term_' . $ancestorId);
                if ($color) {
                    return $color;
                }
            }
        }

        return false;
    }

    /**
     * Get term based on type.
     *
     * @param string|int|WP_Term    $term The term to get
     * @param string                $taxonomy The taxonomy of the term. Default is an empty string.
     */
    private function getTerm($term, string $taxonomy = ''): \WP_Term|false
    {
        if (is_a($term, 'WP_Term')) {
            return $term;
        }

        if (empty($taxonomy)) {
            return false;
        }

        if (is_int($term)) {
            return $this->wpService->getTermBy('term_id', $term, $taxonomy, 'OBJECT');
        }

        if (is_string($term)) {
            return $this->wpService->getTermBy('slug', $term, $taxonomy, 'OBJECT');
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getTermIcon(int|string|\WP_Term $term, string $taxonomy = ''): array|false
    {
        $term = self::getTerm($term, $taxonomy);

        if (empty($term)) {
            return false;
        }

        $termIcon = $this->acfService->getField('icon', 'term_' . $term->term_id);
        $type     = !empty($termIcon['type']) ? $termIcon['type'] : false;
        if ($type === 'svg' && !empty($termIcon['svg']['ID'])) {
            $attachment = $this->wpService->wpGetAttachmentImageUrl($termIcon['svg']['ID'], 'full');
            $result     = $this->wpService->applyFilters(
                'Municipio/getTermIconSvg',
                [
                    'src'         => $attachment,
                    'type'        => $type,
                    'description' => $termIcon['svg']['description'],
                    'alt'         => $termIcon['svg']['description']
                ],
                $term
            );
        } elseif ($type === 'icon' && !empty($termIcon['material_icon'])) {
            $result = $this->wpService->applyFilters(
                'Municipio/getTermIcon',
                [
                    'src'  => $termIcon['material_icon'],
                    'type' => $type
                ],
                $term
            );
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function createOrGetTermIdFromString(string $termString, string $taxonomy): ?int
    {
        $term = $this->wpService->getTermBy('name', $termString, $taxonomy, 'OBJECT');

        if (!$term) {
            $result = $this->wpService->wpInsertTerm($termString, $taxonomy);
            if ($this->wpService->isWpError($result)) {
                return null;
            }
            $termId = $result['term_id'];
        } else {
            $termId = $term->term_id;
        }

        return $termId ?? null;
    }
}
