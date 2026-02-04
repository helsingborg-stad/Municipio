<?php

namespace Municipio\Blocks\ConvertModulesToBlocks;

interface ModuleToBlockInterface
{
    /**
     * Convert a Modularity module to a WP_Block
     *
     * @param \Modularity\Module $module
     * @return \WP_Block
     */
    public function convert(\Modularity\Module $module): \WP_Block;
}
