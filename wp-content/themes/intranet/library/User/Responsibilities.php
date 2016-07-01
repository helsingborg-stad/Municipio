<?php

namespace Intranet\User;

class Responsibilities
{
    public function __construct()
    {
        add_action('wp_ajax_nopriv_autocomplete_responsibilities', array($this, 'autocompleteJson'));
        add_action('wp_ajax_autocomplete_responsibilities', array($this, 'autocompleteJson'));
    }

    public function autocompleteJson()
    {
        if (!isset($_POST['q']) || empty($_POST['q'])) {
            echo '0';
            exit;
        }

        $responsibilities = $this->getAllResponsibilities(sanitize_text_field($_POST['q']));
        $responsibilities = array_slice($responsibilities, 0, 10);
        echo json_encode($responsibilities);
        exit;
    }

    public function getAllResponsibilities($q = null)
    {
        $responsibilities = array();

        global $wpdb;
        $query = $wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = %s", array('user_responsibilities'));
        $results = $wpdb->get_results($query);

        foreach ($results as $result) {
            $result = unserialize($result->meta_value);
            $responsibilities = array_merge($responsibilities, $result);
        }

        // Array unique (case insensitive)
        $responsibilities = array_intersect_key(
            $responsibilities,
            array_unique(array_map('strtolower', $responsibilities))
        );

        if (!is_null($q)) {
            $responsibilities = array_filter($responsibilities, function ($item) use ($q) {
                $item = strtolower($item);
                $q = strtolower($q);

                return (strpos($item, $q) !== false);
            });

            uasort($responsibilities, function ($a, $b) use ($q) {
                $aDistance = levenshtein($a, $q);
                $bDistance = levenshtein($b, $q);
                return ($aDistance < $bDistance) ? -1 : 1;
            });
        }

        return (array) $responsibilities;
    }
}
