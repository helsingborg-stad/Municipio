<?php

namespace Municipio\HooksRegistrar;

interface HooksRegistrarInterface
{
    public function register(Hookable $object): HooksRegistrarInterface;
}
