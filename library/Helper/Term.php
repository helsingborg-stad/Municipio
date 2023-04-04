<?php

namespace Municipio\Helper;

class Term
{
    /**
     * `getTermColour` returns the colour of a term.
     * If no colour is set, it will return the colour of the first ancestor that has a colour set.
     *
     * @param int|string|WP_Term $term The term to get the colour for. Can be a term object, term ID or term slug.
     * @param string $taxonomy The taxonomy of the term. Default is an empty string.
     *
     * @return bool|string A string of the colour of the term in HEX format.
     */
    public static function getTermColour($term, string $taxonomy = '')
    {
        // If no taxonomy is set $term must be a complete term object
        if ('' === $taxonomy && !is_a($term, 'WP_Term')) {
            return false;
        }

        if (is_int($term)) {
            $term = get_term_by('term_id', $term, $taxonomy);
        } elseif (is_string($term)) {
            $term = get_term_by('slug', $term, $taxonomy);
        } elseif (!is_a($term, 'WP_Term')) {
            return false;
        }

        $colour = get_field('colour', $term);
        if ("" !== $colour && !str_starts_with($colour, '#')) {
            $colour = "#{$colour}";
        } elseif ("" === $colour || !$colour) {
            // Use the color and exit the foreach loop when a color is found on an ancestor term
            $ancestors = get_ancestors($term->term_id, $term->taxonomy);
            if (!empty($ancestors)) {
                foreach ($ancestors as $ancestorId) {
                    $colour = get_field('colour', 'term_' . $ancestorId);
                    if ($colour) {
                        return apply_filters('Municipio/getTermColour', $colour, $term, $taxonomy);
                    }
                }
            }
        }

        return apply_filters('Municipio/getTermColour', $colour, $term, $taxonomy);
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
        if ('' === $taxonomy && !is_a($term, 'WP_Term')) {
            return false;
        }

        if (is_int($term)) {
            $term = get_term_by('term_id', $term, $taxonomy);
        } elseif (is_string($term)) {
            $term = get_term_by('slug', $term, $taxonomy);
        } elseif (!is_a($term, 'WP_Term')) {
            return false;
        }

        $termIcon = get_field('icon', $term);
        $type = $termIcon['type'];

        if ($type === 'svg') {
            $attachment = wp_get_attachment_image_url($termIcon['svg']['ID'], 'full');
            $result = apply_filters(
                'Municipio/getTermIconSvg',
                ['src' => $attachment, 'type' => $type, 'description' => $termIcon['svg']['description']],
                $term
            );
        } elseif ($type === 'icon') {
            $result = apply_filters(
                'Municipio/getTermIcon',
                ['src' => $termIcon['material_icon'], 'type' => $type],
                $term
            );
        } else {
            $result = false;
        }

        return $result;
    }
}
