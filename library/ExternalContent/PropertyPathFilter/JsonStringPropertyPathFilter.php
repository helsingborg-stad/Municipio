<?php

namespace Municipio\ExternalContent\PropertyPathFilter;

class JsonStringPropertyPathFilter {

    public function filter(string $json) {
        if (!json_decode($json)) {
            throw new \InvalidArgumentException('Input parameter is not a json string.');
        }
    }
}