<?php

namespace Municipio\Integrations\MiniOrange\Config;

interface MiniOrangeConfigInterface
{
    public function isEnabled(): bool;
    public function requireSsoLogin(): bool;
    public function getCurrentProvider(): ?string;
    public function getUserGroupTaxonomy(): string;
}
