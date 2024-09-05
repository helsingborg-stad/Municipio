<?php

namespace Municipio\Config;

interface ConfigFactoryInterface
{
    public function createConfig(): ConfigInterface;
}
