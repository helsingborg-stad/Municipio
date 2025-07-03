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
     * Modify the column elements
     * @param string $content The blocks content as a string
     * @param array $block The blocks settings and its contents
     * @return string Returns the modified content as a string
     */
    public function renderBlockColumns(string $content, array $block): string
    {
        if (is_admin()) {
            return $content;
        }

        if (!$this->isBlock('core/columns', $block)) {
            return $content;
        }

        $gridClasses     = $this->createGridClassesArray($block['innerBlocks']);

        if(isset($_GET['noblockcol'])) {
           return $content;
        }

        $modifiedColumns = $this->processBlockColumns($content, $gridClasses);
        return '<div class="o-grid">' . "\n" . implode("\n", $modifiedColumns) . "\n" . '</div>';
    }

    /**
     * Modify the column elements
     * @param string $content The blocks content as a string
     * @param array $gridClasses Array of columns classes strings
     * @return array
     */
    private function processBlockColumns(string $content, array $gridClasses)
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true); // Suppress HTML5 parsing warnings
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);
        $elements = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " wp-block-column ")]');

        $modifiedColumns = [];
        $index = 0;

        foreach ($elements as $child) {
            if (!($child instanceof \DOMElement)) {
                continue;
            }

            if ($index >= count($gridClasses)) {
                break;
            }

            $class = $child->getAttribute('class');
            $child->setAttribute(
                'class',
                implode(
                    ' ',
                    [
                        $gridClasses[$index],
                        'o-grid-column-block',
                        str_replace('wp-block-column', '', $class),
                    ]
                )
            );
            $modifiedColumns[] = $child->C14N();
            $index++;
        }

        return $modifiedColumns;
    }

    /**
     * Creates an array of column classes
     * @param array $innerBlocks Array of gutenberg blocks
     * @return array
     */
    private function createGridClassesArray(array $innerBlocks): array
    {
        $columnsCount = $this->countColumns($innerBlocks);
        $gridClasses  = [];
        foreach ($innerBlocks as $block) {
            if (!empty($block['attrs']['width'])) {
                $number        = $this->blockWidthToNumber($block['attrs']['width'], $columnsCount);
                $gridClasses[] = $this->getGridClass($number);
            } else {
                $gridClasses[] = $this->getGridClass($columnsCount);
            }
        }

        return $gridClasses;
    }


    /**
     * Calculate column size for specific column
     * @param string $width Width of a column
     * @param int $columnsCount Amount of columns
     * @return float
     */
    private function blockWidthToNumber(string $width, int $columnsCount): float
    {
        if (is_string($width)) {
            $number = floatval($width);

            if (!empty($number) && is_numeric($number) && $number <= 100) {
                $number = round(($number / 100) * 12);
                $number = 12 / $number;
            }
        }
        return isset($number) && $number <= 12 ? $number : $columnsCount;
    }

    /**
     * Counts the number of items in inner blocks
     * @param array $innerBlocks Array of gutenberg blocks
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
     * @param  float $numberOfColumns Number to calculate a columns size
     * @return string
     */
    private function getGridClass(float $numberOfColumns): string
    {
        $stack = [];
        if (!isset($numberOfColumns) || !is_numeric($numberOfColumns)) {
            $numberOfColumns = 4;
        }

        $stack[] = \Municipio\Helper\Html::createGridClass(1);

        if ($numberOfColumns == 1.5) {
            $stack[] = \Municipio\Helper\Html::createGridClass(2, 'md');
            $stack[] = \Municipio\Helper\Html::createGridClass(1.5, 'lg');
        }

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
