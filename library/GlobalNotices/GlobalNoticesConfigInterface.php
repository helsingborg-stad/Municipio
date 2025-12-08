<?php

namespace Municipio\GlobalNotices;

interface GlobalNoticesConfigInterface
{
  /**
   * Get the available locations for global notices.
   *
   * @return array
   */
  public function getLocations(): array;

  /**
   * Get the key used for notice data.
   *
   * @return string
   */
  public function getNoticeDataKey(): string;
}