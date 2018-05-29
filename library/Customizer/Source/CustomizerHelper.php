<?php

namespace Municipio\Customizer\Source;

class CustomizerHelper
{
    public static function uniqueKey($id, $array)
    {
        $id = sanitize_title($id);

        if (isset($array[$id])) {
            $i = 1;

            while (isset($array[$id . '-' . $i])) {
                $i++;
            }

            return $id . '-' . $i;
        }

        return $id;
    }

    public function generateIds($format, $total)
    {
        $sidebars = array();

        $i = 1;
        for ($i = 1; $i <= $total; $i++) {
            $sidebars[] = sprintf($format, $i);
        }

        return $sidebars;
    }
}
