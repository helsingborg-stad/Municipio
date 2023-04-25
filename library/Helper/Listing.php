<?php

namespace Municipio\Helper;

class Listing
{
    public static function createListingItem(string $label, string $icon, string $href = '')
    {
        if (!empty($label)) {
            return apply_filters(
                'Municipio/Helper/Listing/createListingItem',
                [
                'label' => $label,
                'icon' => ['icon' => $icon, 'size' => 'md'],
                'href' => $href
                ]
            );
        }

        return false;
    }
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
            $term->icon = \Municipio\Helper\Term::getTermIcon($term);
            $terms[] = $term;
        }
        return $terms;
    }
}
