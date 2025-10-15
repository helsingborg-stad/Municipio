<?php

namespace Modularity\HooksRegistrar;

interface HooksRegistrarInterface
{
    public function register(Hookable $object): HooksRegistrarInterface;
}
