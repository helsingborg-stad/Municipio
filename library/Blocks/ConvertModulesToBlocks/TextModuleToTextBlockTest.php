<?php

namespace Municipio\Blocks\ConvertModulesToBlocks;

use Modularity\Module;
use PHPUnit\Framework\TestCase;
use WP_Block;

class TextModuleToTextBlockTest extends TestCase
{
    public function testConvert()
    {
        $module = $this->createMock(Module::class);
        $module
            ->method('getFields')
            ->willReturn([
                'title' => 'Title from module',
                'content' => 'Text from module',
                'hide_box_frame' => false,
            ]);

        $block = (new TextModuleToTextBlock())->convert($module);

        $this->assertInstanceOf(WP_Block::class, $block);
        $this->assertEquals('acf/text', $block->blockName);
        $this->assertEquals('Title from module', $block->attrs['data']['field_block_title']);
        $this->assertEquals('Text from module', $block->attrs['data']['field_60ab6d956843c']);
        $this->assertEquals('0', $block->attrs['data']['field_5891b6038c120']);
    }
}
