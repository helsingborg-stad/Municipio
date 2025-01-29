<?php

namespace Municipio\GlobalNotices;

interface GetAndApplyGlobalNoticesInterface
{
  public function getGlobalNotices(): array;
  public function mapGlobalNotice(array $notice): ?array;
  public function filterByConstraints(array $notice): bool;
  public function getGlobalNoticesByLocation(string $location): array;
  public function filterViewData(array $data): array;
}