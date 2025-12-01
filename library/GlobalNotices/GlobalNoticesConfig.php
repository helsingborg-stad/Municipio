<?php

namespace Municipio\GlobalNotices;

enum GlobalNoticeLocation: string
{
    case TOAST   = 'toast';
    case BANNER  = 'banner';
    case CONTENT = 'content';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

class GlobalNoticesConfig implements GlobalNoticesConfigInterface
{
    public function getLocations(): array
    {
        return GlobalNoticeLocation::values();
    }

    public function getNoticeDataKey(): string
    {
        return 'notice';
    }
}
