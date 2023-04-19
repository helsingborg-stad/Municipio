<?php

namespace Municipio\Helper;

/**
 * Translation registry
 * 
 * Stores and manages translations.
 */
final class TranslationRegistry
{
    private static object $collection;

    public function __construct()
    {
        self::$collection = new \stdClass();
    }

    public function add(string $key, string $value): void
    {
        self::$collection->{$key} = $value;
    }

    public function get(string $key): string
    {
        if (!isset(self::$collection->$key)) {
            trigger_error("Key $key does not exist in registry", E_USER_NOTICE);
            return '';
        }

        return self::$collection->$key;
    }

    public function update(string $key, string $newValue) {

        if( isset(self::$collection->$key) ) {
            self::$collection->{$key} = $newValue;
        } else {
            $this->add($key, $newValue);
            trigger_error("Key $key does not exist in registry", E_USER_NOTICE);
        }
    }

    public function getCollection(): object
    {
        return self::$collection;
    }
}
