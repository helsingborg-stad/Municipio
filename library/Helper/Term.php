<?php

namespace Municipio\Helper;

/**
 * Class Term
 */
class Term
{
    /**
     * `getTermColour` returns the colour of a term.
     * If no colour is set, it will return the colour of the first ancestor that has a colour set.
     *
     * @param int|string|WP_Term $term The term to get the colour for. Can be a term object, term ID or term slug.
     * @param string $taxonomy The taxonomy of the term. Default is an empty string.
     *
     * @return false|string A string of the colour of the term in HEX format.
     */
    public static function getTermColour($term, string $taxonomy = '')
    {
        $term = self::getTerm($term, $taxonomy);

        if (empty($term)) {
            return false;
        }

        $colour = get_field('colour', $term);
        if (is_string($colour) && "" !== $colour && !str_starts_with($colour, '#')) {
            $colour = "#{$colour}";
        } elseif ("" === $colour || !$colour) {
            $colour = self::getAncestorTermColor($term);
        }

        return apply_filters('Municipio/getTermColour', $colour, $term, $taxonomy);
    }

    /**
     * Gets term color from ancestor term
     * @param WP_Term $term The term to get the color for. Can be a term object, term ID or term slug.
     *
     * @return string|false
     */
    private static function getAncestorTermColor(\WP_Term $term)
    {
        $ancestors = get_ancestors($term->term_id, $term->taxonomy, 'taxonomy');
        if (!empty($ancestors)) {
            foreach ($ancestors as $ancestorId) {
                $color = get_field('colour', 'term_' . $ancestorId);
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
    private static function getTerm($term, string $taxonomy = '')
    {
        if (is_a($term, 'WP_Term')) {
            return $term;
        }

        if (empty($taxonomy)) {
            return false;
        }

        if (is_int($term)) {
            return get_term_by('term_id', $term, $taxonomy);
        }

        if (is_string($term)) {
            return get_term_by('slug', $term, $taxonomy);
        }

        return false;
    }

    /**
     * Alias with American English spelling for getTermColour()
     */
    public static function getTermColor($term, string $taxonomy = '')
    {
        return self::getTermColour($term, $taxonomy);
    }

    /**
     * Returns the icon for a given term and taxonomy.
     *
     * @param mixed $term The term to retrieve the icon for. Can be a WP_Term object, ID, or slug.
     * @param string $taxonomy The taxonomy of the term. (not needed if $term is a WP_Term object)
     *
     * @return mixed|array|false The icon of the term and the icon type or false if it can't be found.
     */
    public static function getTermIcon($term, string $taxonomy = '')
    {
        $term = self::getTerm($term, $taxonomy);

        if (empty($term)) {
            return false;
        }

        $termIcon = get_field('icon', $term);
        $type     = !empty($termIcon['type']) ? $termIcon['type'] : false;
        if ($type === 'svg' && !empty($termIcon['svg']['ID'])) {
            $attachment = wp_get_attachment_image_url($termIcon['svg']['ID'], 'full');
            $result     = apply_filters(
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
            $result = apply_filters(
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
}
