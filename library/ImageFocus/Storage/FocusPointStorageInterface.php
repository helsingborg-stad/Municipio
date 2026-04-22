<?php

namespace Municipio\ImageFocus\Storage;

use WpService\WpService;

interface FocusPointStorageInterface
{
    public function get(int $attachmentId): ?array;

    public function set(int $attachmentId, array $focus): bool;
}
