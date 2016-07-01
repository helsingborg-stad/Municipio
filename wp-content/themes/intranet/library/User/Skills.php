<?php

namespace Intranet\User;

class Skills
{
    public function __construct()
    {
        add_action('wp_ajax_nopriv_autocomplete_skills', array($this, 'autocompleteJson'));
        add_action('wp_ajax_autocomplete_skills', array($this, 'autocompleteJson'));
    }

    public function autocompleteJson()
    {
        if (!isset($_POST['q']) || empty($_POST['q'])) {
            echo '0';
            exit;
        }

        $skills = $this->getAllSkills(sanitize_text_field($_POST['q']));
        $skills = array_slice($skills, 0, 10);
        echo json_encode($skills);
        exit;
    }

    public function getAllSkills($q = null)
    {
        $skills = array();

        global $wpdb;
        $query = $wpdb->prepare("SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = %s", array('user_skills'));
        $results = $wpdb->get_results($query);

        foreach ($results as $result) {
            $result = unserialize($result->meta_value);
            $skills = array_merge($skills, $result);
        }

        // Array unique (case insensitive)
        $skills = array_intersect_key(
            $skills,
            array_unique(array_map('strtolower', $skills))
        );

        if (!is_null($q)) {
            $skills = array_filter($skills, function ($item) use ($q) {
                $item = strtolower($item);
                $q = strtolower($q);

                return (strpos($item, $q) !== false);
            });

            uasort($skills, function ($a, $b) use ($q) {
                $aDistance = levenshtein($a, $q);
                $bDistance = levenshtein($b, $q);
                return ($aDistance < $bDistance) ? -1 : 1;
            });
        }

        return (array) $skills;
    }
}
