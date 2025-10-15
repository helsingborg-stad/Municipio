<?php

namespace Modularity\Helper;

class AcceptanceLabels {
    public static function getLabels() {
        return [
            'knownLabels' => [
                'title' => __('We need your consent to continue', 'modularity'),
                'info' => sprintf(__('This part of the website shows content from %s. By continuing, <a href="%s"> you are accepting GDPR and privacy policy</a>.', 'modularity'), '{SUPPLIER_WEBSITE}', '{SUPPLIER_POLICY}'),
                'button' => __('I understand, continue.', 'modularity'),
            ],
            'unknownLabels' => [
                'title' => __('We need your consent to continue', 'modularity'),
                'info' => __('This part of the website shows content from another website ({SUPPLIER_WEBSITE}). By continuing, you are accepting GDPR and privacy policy.', 'modularity'),
                'button' => __('I understand, continue.', 'modularity'),
            ],
        ];
    }
}