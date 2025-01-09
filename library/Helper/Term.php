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
     * Alias with American English spelling for getTermColour()
     *
     * @deprecated Use Municipio\Helper\Term\Term::getTermColor() instead.
     */
    public static function getTermColor($term, string $taxonomy = '')
    {
        trigger_error('Use Municipio\Helper\Term\Term::getTermColor() instead.', E_USER_DEPRECATED);
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
        trigger_error('Use Municipio\Helper\Term\Term::getTermIcon() instead.', E_USER_DEPRECATED);
        $termHelper = new TermHelper(WpService::get(), AcfService::get());

        return $termHelper->getTermIcon($term, $taxonomy);
    }
}
