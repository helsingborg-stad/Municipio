<?php

/**
 * Class Blocks Columns
 * Replaces wordpress columns classes with our own.
 */

namespace Municipio\Blocks;

class Columns
{
    public function __construct()
    {
        add_filter('render_block', array($this, 'renderBlockColumns'), 15, 2);
    }

    /**
     * Checks if the block is of the correct type
     *
     * @param [type] $name
     * @param [type] $object
     * @return boolean
     */
    private function isBlock(string $name, array $object): bool
    {
        return $object['blockName'] == $name;
    }

    /**
     * Renders the block columns (wrapper)
     */
    public function renderBlockColumns(string $content, array $block): string
    {
        if (!$this->isBlock('core/columns', $block)) {
            return $content;
        }

        //Get number of columns
        $gridClass = (string) $this->getGridClass(
            $this->countColumns($block['innerBlocks'])
        );

        //Load doc as string
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $content);

        //Get the columns and its contents
        $result = [];

        foreach ($doc->getElementsByTagName('*') as $child) {
            if (strpos($child->getAttribute('class'), 'wp-block-column') !== false) {
                $child->setAttribute(
                    'class',
                    implode(
                        ' ',
                        [
                            $gridClass,
                            str_replace('wp-block-column', '', $child->getAttribute('class'))
                        ]
                    )
                );
                $child->setAttribute('style', false);

                $result[] = $child->c14n();
            }
        }

        return '<div class="o-grid">' . "\n" . implode("\n", $result) . "\n" . '</div>';
    }

    /**
     * Counts the number of items in inner blocks
     *
     * @param array $innerBlocks
     * @return int
     */
    private function countColumns(array $innerBlocks): int
    {
        if (is_array($innerBlocks)) {
            return (int) count($innerBlocks);
        }
        return 1;
    }

    /**
     * Create a grid column size
     * @param  array $numberOfColumns
     * @return string
     */
    private function getGridClass(int $numberOfColumns): string
    {
        $stack = [];

        if (!isset($numberOfColumns) || !is_numeric($numberOfColumns)) {
            $numberOfColumns = 4;
        }

        $stack[] = \Municipio\Helper\Html::createGridClass(1);

        if ($numberOfColumns == 2) {
            $stack[] = \Municipio\Helper\Html::createGridClass(2, 'md');
            $stack[] = \Municipio\Helper\Html::createGridClass(2, 'lg');
        }

        if ($numberOfColumns == 3) {
            $stack[] = \Municipio\Helper\Html::createGridClass(2, 'md');
            $stack[] = \Municipio\Helper\Html::createGridClass(3, 'lg');
        }

        if ($numberOfColumns == 4) {
            $stack[] = \Municipio\Helper\Html::createGridClass(2, 'sm');
            $stack[] = \Municipio\Helper\Html::createGridClass(3, 'md');
            $stack[] = \Municipio\Helper\Html::createGridClass(4, 'lg');
        }

        return implode(' ', $stack);
    }
}
