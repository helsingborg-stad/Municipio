<?php

namespace Modularity\Upgrade\Version;

class V3 implements versionInterface {
    private $db;

    public function __construct(\wpdb $db) {
        $this->db = $db;
    }

    public function upgrade(): bool
    {
        $options = get_option('modularity-options');
        if (is_array($options['enabled-modules'])) {
            $options['enabled-modules'][] = "mod-manualinput"; 
        }
        
        update_option('modularity-options', $options);

        return true;
    }
}