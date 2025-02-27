<?php

namespace Municipio\ExternalContent\PropertyPathFilter;

class GetValueByPathFromArray
{
    public function getValueByPath(array $array, $path): mixed
    {
        $path = explode('.', $path);

        return $this->getValueByPathRecursive($array, $path);
    }

    private function getValueByPathRecursive(array $array, array $path): mixed
    {
        $key = array_shift($path);

        if (empty($path)) {
            return $array[$key];
        }

        if (!is_array($array[$key]) || array_keys($array[$key]) === range(0, count($array[$key]) - 1)) {
            return array_map(function ($item) use ($path) {
                return $this->getValueByPathRecursive($item, $path);
            }, $array[$key]);
        }


        return $this->getValueByPathRecursive($array[$key], $path);
    }
}
