<?php

namespace Municipio\Customizer\Source;

class CustomizerRepeaterInput extends CustomizerHelper
{
    public $field, $id, $identifierKey, $repeater;

    public function __construct($field, $id, $identifierKey)
    {
        $this->getRepeater($field, $id, $identifierKey);
    }

    public function getRepeater($field, $id, $identifierKey)
    {
        $this->field = $field;
        $this->id = $id;
        $this->identifierKey = $identifierKey;
        $repeater = self::sanitizeRepeater($this->field, $this->id, $this->identifierKey);

        if (!is_array($repeater) || empty($repeater)) {
            return;
        }

        foreach ($repeater as $item) {
           $this->repeater[] = apply_filters('Municipio/Customizer/Source/CustomizerRepeaterInput', $item, $field, $id, $identifierKey);
        }

        $this->hasItems = $this->hasItems();
    }

    public function hasItems()
    {
        if (empty($this->repeater) || !is_array($this->repeater)) {
            return false;
        }

        return true;
    }

    public static function sanitizeRepeater($field, $id, $identifierKey)
    {
        if (!is_array(get_field($field, $id)) || empty(get_field($field, $id))) {
            return false;
        }

        $items = array();
        foreach (get_field($field, $id) as $item) {
            if (!isset($item[$identifierKey]) || empty($item[$identifierKey])) {
                continue;
            }

            $item[$identifierKey] = self::uniqueKey($item[$identifierKey], $items);
            $items[$item[$identifierKey]] = $item;
        }

        if (!empty($items)) {
            return $items;
        }

        return false;
    }
}
