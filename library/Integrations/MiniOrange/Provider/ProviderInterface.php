<?php

namespace Municipio\Integrations\MiniOrange\Provider;

interface ProviderInterface
{
  /**
   * Get the provider name.
   *
   * @return string
   */
  public function getName(): string;

  /**
   * Authenticate the user.
   *
   * @param array $credentials
   * @return bool
   */
  public function authenticate(array $credentials): bool;

  /**
   * Get the user information.
   *
   * @return array
   */
  public function getUserInfo(): array;
}