<?php

namespace Municipio\Helper;

/**
 * Class ListingTest
 */
class Listing
{
    /**
     * Create a listing item.
     *
     * @param string $label The label for the listing item.
     * @param string $href  The href attribute for the listing item (default is an empty string).
     * @param array  $icon  An associative array containing icon properties (default is an empty array).
     *
     * @return array|false Returns an array representing the listing item, or false if label is empty.
     */
    public static function createListingItem(string $label, string $href = '', array $icon = [])
    {
        if (!empty($label)) {
            $icon['size'] = 'md';
            return apply_filters(
                'Municipio/Helper/Listing/createListingItem',
                [
                    'label' => $label,
                    'icon'  => $icon,
                    'href'  => $href
                ]
            );
        }

        return false;
    }

    /**
     * Get terms with associated icons.
     *
     * @param array $termIds An array of term IDs.
     *
     * @return array|false An array of term objects with associated icons, or false if termIds is empty.
     */
    public static function getTermsWithIcon(array $termIds = [])
    {
        if (empty($termIds)) {
            return false;
        }

        $terms = [];
        foreach ($termIds as $termId) {
            $term = get_term($termId);
            if (!$term || is_wp_error($term)) {
                continue;
            }

            $termHelper = new \Municipio\Helper\Term\Term(
                \Municipio\Helper\WpService::get(),
                \Municipio\Helper\AcfService::get()
            );

            $term->icon = $termHelper->getTermIcon($term);
            $terms[]    = $term;
        }
        return $terms;
    }
}
