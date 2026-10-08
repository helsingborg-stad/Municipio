<?php

declare(strict_types=1);

namespace Municipio\Chat\Provider;

interface ChatProviderResolverInterface
{
    public function resolve(array $assistant): ChatProviderInterface|\WP_Error;
}
