<?php

namespace Municipio\BackdropBanner;

interface BlockRendererInterface
{
    public function render(array $attributes): string;
}