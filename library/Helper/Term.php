<?php

namespace Municipio\Helper;

class Term
{
    /**
     * `getTermColour` returns the colour of a term
     *
     * @param object term The term to get the colour for. Can be a term object, term ID or term slug.
     *
     * @return bool|string A string of the colour of the term in HEX format.
     */
    public static function getTermColour($term, string $taxonomy = '')
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

        $colour = get_field('colour', $term);
        if (is_string($colour) && !str_starts_with($colour, '#')) {
            $colour = "#{$colour}";
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
     * It returns the URL of the icon associated with a given term
     *
     * @param string|int|object term The term to get the icon for. Can be a WP_Term object, term ID or term slug.
     *
     * @return bool|string The URL of the icon image for the term.
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

        $attachmentId = get_field('icon', $term);

        return apply_filters('Municipio/getTermIcon', wp_get_attachment_image_url($attachmentId), $term, $taxonomy);
    }
}
