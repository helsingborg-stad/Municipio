<?php

namespace Municipio\Helper;

class Acf
{
    /**
     * Get ACF field key by fieldname, has to be run at action 'init' or later
     * @param string $fieldName The field name
     * @param string $fieldId Field ID can be post id, widget id, 'options' etc
     * @return boolean/string
     */
    public static function getFieldKey($fieldName, $fieldId)
    {
        if (isset(get_field_objects($fieldId)[$fieldName]['key'])) {
            return get_field_objects($fieldId)[$fieldName]['key'];
        }

        return false;
    }
}
