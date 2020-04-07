<?php

namespace Municipio\Helper;

class FormatObject
{
    /**
     * Camelcase snake_case object 
     * 
     * @param   object   $object The object, snake case
     * 
     * @return  object   $object The object, camel case
     */
    public static function camelCase($object)
    {
        return (object) self::mapArrayKeys(function($string) {
            return lcfirst(implode('', array_map('ucfirst', explode('_', strtolower($string)))));
        }, (array) $object);
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
