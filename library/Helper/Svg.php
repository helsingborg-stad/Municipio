<?php

namespace Municipio\Helper;

class Svg
{
    public static function extract($symbol, $classes = '')
    {
        $symbol = file_get_contents($symbol);

        //Get by dom method
        if (class_exists('DOMDocument')) {
            $doc = new \DOMDocument();
            if ($doc->loadXML($symbol) === true) {
                try {
                    $doc->getElementsByTagName('svg');

                    $svg = $doc->getElementsByTagName('svg');
                    if ($svg->item(0)->C14N() !== null) {
                        $symbol = $svg->item(0)->C14N();
                    }
                } catch (exception $e) {
                    error_log("Error loading SVG file to header or footer.");
                }
            }
        }

        //Filter tags & comments (if above not applicated)
        $symbol = preg_replace('/<\?xml.*?\/>/im', '', $symbol); //Remove XML
        $symbol = preg_replace('/<!--(.*)-->/Uis', '', $symbol); //Remove comments & javascript

        if (strlen($classes) > 0) {
            $symbol = str_replace('<svg', '<svg class="' . $classes . '"', $symbol);
        }

        return $symbol;
    }
}
