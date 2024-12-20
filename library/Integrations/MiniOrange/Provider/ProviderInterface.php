<?php

namespace Municipio\Integrations\MiniOrange\Provider;

interface ProviderInterface
{
  public function identifier(): string;
  public function getMap(): array;
}