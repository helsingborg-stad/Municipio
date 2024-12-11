<?php

namespace Municipio\Admin\Integrations\MiniOrange\Config;

interface MiniOrangeConfigInterface
{
  public function isEnabled(): bool;
  public function requireSsoLogin(): bool;
}