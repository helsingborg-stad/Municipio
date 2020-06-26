<?php

namespace Municipio\Helper;

class FormatObject
{

    /**
     * Camelcase snake_case object or string 
     * 
     * @param   object|array|string   $object The item, snake case
     * 
     * @return  object|array|string   $object The item, camel case
     */
    public static function camelCase($item)
    {
        switch ($item) {
            case is_array($item):
            case is_object($item):
                return self::camelCaseObject($item); 
                break;
            case is_string($item):
                return self::camelCaseString($item); 
                break;
            default:
               throw new Exception("Input is not a array, object or string. Cannot camelCase value.");
        }
    }

    /**
     * Camelcase snake_case object or array
     * 
     * @param   object|array   $object The object, snake case
     * 
     * @return  object|array   $object The object, camel case
     */
    public static function camelCaseObject($object)
    {
        return (object) self::mapArrayKeys(function($string) {
            return lcfirst(implode('', array_map('ucfirst', explode('_', strtolower($string)))));
        }, (array) $object);
    }

    /**
     * Camelcase snake_case string 
     * 
     * @param   string   $string The string, snake case
     * 
     * @return  string   $string The string, camel case
     */
    public static function camelCaseString($string)
    {
        return lcfirst(implode('', array_map('ucfirst', explode('_', strtolower($string)))));
    }

    /**
     * Replaces old keys with new (recursivley)
     * 
     * @param   function    $func    Function for transformation of key
     * @param   array       $array   The array to filter
     * 
     * @return  array       $return  The array with renamed keys
     */
    public static function mapArrayKeys(callable $func, array $array) {
        $return = array();
        foreach ($array as $key => $value) {
          $return[$func($key)] = is_array($value) ? self::mapArrayKeys($func, $value) : $value;
        }
        return $return;
    }
}
