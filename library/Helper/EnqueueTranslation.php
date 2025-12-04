<?php

namespace Municipio\Helper;

class EnqueueTranslation
{
  public function __construct(private string $objectKey, private array $translations) {}

  /**
   * Localize translations to a script.
   *
   * @param string $handle The handle name for the script.
   * @return void
   */
  public function getTranslations(): array
  {
    return $this->translations;
  }

  public function getObjectName(): string
  {
    return $this->objectKey;
  }
}