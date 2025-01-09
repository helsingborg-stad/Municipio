<?php

namespace Municipio\Helper;

use Municipio\Helper\Term\Term as TermHelper;

/**
 * Class Term
 *
 * @deprecated Use Municipio\Helper\Term\Term instead.
 */
class Term
{
    /**
     * `getTermColour` returns the colour of a term.
     * If no colour is set, it will return the colour of the first ancestor that has a colour set.
     *
     * @deprecated Use Municipio\Helper\Term\Term::getTermColor() instead.
     *
     * @param int|string|WP_Term $term The term to get the colour for. Can be a term object, term ID or term slug.
     * @param string $taxonomy The taxonomy of the term. Default is an empty string.
     *
     * @return false|string A string of the colour of the term in HEX format.
     */
    public static function getTermColour($term, string $taxonomy = '')
    {
        _doing_it_wrong(__METHOD__, 'Use Municipio\Helper\Term\Term::getTermColor() instead.');
        return self::getTermColor($term, $taxonomy);
    }

    /**
     * Alias with American English spelling for getTermColour()
     *
     * @deprecated Use Municipio\Helper\Term\Term::getTermColor() instead.
     */
    public static function getTermColor($term, string $taxonomy = '')
    {
        _doing_it_wrong(__METHOD__, 'Use Municipio\Helper\Term\Term::getTermColor() instead.');
        $termHelper = new TermHelper(WpService::get(), AcfService::get());
        return $termHelper->getTermColor($term, $taxonomy);
    }

    /**
     * Returns the icon for a given term and taxonomy.
     *
     * @deprecated Use Municipio\Helper\Term\Term::getTermIcon() instead.
     *
     * @param mixed $term The term to retrieve the icon for. Can be a WP_Term object, ID, or slug.
     * @param string $taxonomy The taxonomy of the term. (not needed if $term is a WP_Term object)
     *
     * @return mixed|array|false The icon of the term and the icon type or false if it can't be found.
     */
    public static function getTermIcon($term, string $taxonomy = '')
    {
        _doing_it_wrong(__METHOD__, 'Use Municipio\Helper\Term\Term::getTermIcon() instead.');
        $termHelper = new TermHelper(WpService::get(), AcfService::get());

        return $termHelper->getTermIcon($term, $taxonomy);
    }
}
