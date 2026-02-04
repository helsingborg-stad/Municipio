<?php

namespace Municipio\Blocks\ConvertModulesToBlocks;

use Modularity\Module;
use WP_Block;

class TextModuleToTextBlock implements ModuleToBlockInterface
{
    public function convert(Module $module): WP_Block
    {
        return new WP_Block([
            'blockName' => 'acf/text',
            'attrs' => [
                'data' => [
                    'field_block_title' => 'Title from module', // Title
                    'field_636e42408367e' => 'auto', // Language
                    'field_60ab6d956843c' => 'Text from module', // Content
                    'field_5891b6038c120' => '0', // Hide text box,
                ],
            ],
        ]);
    }
}
