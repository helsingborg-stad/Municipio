<?php

namespace Modularity\Upgrade\Version;

use Modularity\Upgrade\Version\V5;

class V6 implements versionInterface {
    private versionInterface $v5;

    public function __construct(\wpdb $db) {
        $this->v5 = new V5($db);
    }

    public function upgrade(): bool
    {
        $this->v5->upgrade();

        return true;
    }
}