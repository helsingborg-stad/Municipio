<?php

namespace Municipio\Helper\Constant;

class Constant implements ConstantInterface {
    public function defined(string $name): bool
    {
        return defined($name);
    }

    public function constant(string $name): mixed
    {
        return constant($name);
    }
}