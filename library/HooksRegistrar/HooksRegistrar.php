<?php

namespace Municipio\HooksRegistrar;

class HooksRegistrar implements HooksRegistrarInterface
{
    public function register(Hookable $object): HooksRegistrarInterface
    {
        $object->addHooks();

        return $this;
    }
}
