<?php

namespace Modularity\Module\Archive;

use Modularity\Helper\WpService;

class Archive extends \Modularity\Module {
    public $slug = 'archive';
    public $isBlockCompatible = true;

    public function init() {
        $this->nameSingular = WpService::get()->__("Archive", 'municipio');
        $this->namePlural   = WpService::get()->__("Archives", 'municipio');
        $this->description  = WpService::get()->__("Outputs an archive module.", 'municipio');
    }
}
