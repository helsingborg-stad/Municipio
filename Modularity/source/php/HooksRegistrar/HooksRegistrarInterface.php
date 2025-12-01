<?php

declare(strict_types=1);

namespace Modularity\HooksRegistrar;

interface HooksRegistrarInterface
{
    public function register(Hookable $object): HooksRegistrarInterface;
}
