<?php

namespace Municipio\Navigation\Builders;

interface BuilderInterface {
    /**
     * @returns MenuItem[]
     */
    public function build():array;
}