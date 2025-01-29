<?php

namespace Municipio\Helper;

use DOMDocument;

/**
 * Class FormatObject
 */
class FormatObject
{
    /**
     * Camelcase snake_case object or string
     *
     * @param   object|array|string   $object The item, snake case
     *
     * @return  object|string   $object The item, camel case
     */
    public static function camelCase($item)
    {
        if (is_array($item) || is_object($item)) {
            return self::camelCaseObject($item);
        } elseif (is_string($item)) {
            return self::camelCaseString($item);
        } else {
            throw new \Exception("Input is not an array, object, or string. Cannot camelCase value.");
        }
    }

    /**
     * Camelcase snake_case object or array
     *
     * @param   object|array   $object The object, snake case
     *
     * @return  object          $object The object, camel case
     */
    public static function camelCaseObject($object): object
    {
        return (object) self::mapArrayKeys(function ($string) {
            return self::camelCaseString($string);
        }, (array) $object);
    }

    /**
     * Camelcase string
     *
     * @param   string   $string The string, snake case
     *
     * @return  string   $string The string, camel case
     */
    public static function camelCaseString(string $string): string
    {
        if (preg_match('/^[a-z]+([A-Z][a-z]*)*$/m', $string)) {
            return $string;
        }

        return lcfirst(implode('', array_map('ucfirst', explode('_', str_replace('-', '_', strtolower($string))))));
    }

    /**
     * Replaces old keys with new (recursivley)
     *
     * @param   function    $func    Function for transformation of key
     * @param   array       $array   The array to filter
     *
     * @return  array       $return  The array with renamed keys
     */
    public static function mapArrayKeys(callable $func, array $array): array
    {
        $return     = array();
        $keysToSkip = ['classList', 'attributeList'];
        foreach ($array as $key => $value) {
            $return[$func($key)] = is_array($value) && !in_array($key, $keysToSkip) ?
            self::mapArrayKeys($func, $value) : $value;
        }
        return $return;
    }

    /**
     * It takes a string of HTML, creates a new DOMDocument, loads the HTML into the DOMDocument, and
     * then imports the root node of the DOMDocument into the DOMDocument that was passed in
     *
     * @param DOMDocument doc The DOMDocument object that you want to add the node to.
     * @param string str The string to be converted to a DOMNode
     *
     * @return A DOMNode object.
     */
    public static function createNodeFromString(DOMDocument $doc, string $str): \DOMNode
    {
        $d = new \DOMDocument();
        $d->loadHTML('<?xml encoding="utf-8" ?>' . $str, LIBXML_NOERROR);
        return $doc->importNode($d->documentElement, true);
    }
}
