<?php

namespace Municipio\Customizer\Applicators;

interface ApplicatorCacheInterface
{
    public function tryClearCache(): bool;
}
