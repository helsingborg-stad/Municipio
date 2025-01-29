<?php

namespace Municipio\Library\GlobalNotices;

use AcfService\AcfService;

enum GlobalNoticeLocation: string
{
    case TOAST = 'toast';
    case BANNER = 'banner';
    case MODAL = 'modal';
}

class GetGlobalNotices
{
    public function __construct(private AcfService $wpService)
    {
    }

    /**
     * Get global notices
     * 
     * @return array
     */
    public function getGlobalNotices(): array
    {
        return $this->wpService->getField('global_notices', 'option') ?? [];
    }

    /**
     * Get global notices by location
     * 
     * @param GlobalNoticeLocation $location
     * 
     * @return array
     */
    public function getGlobalNoticesByLocation(GlobalNoticeLocation $location): array
    {
        return array_values(array_filter(
            $this->getGlobalNotices(),
            fn($globalNotice) => isset($globalNotice['location']) && $globalNotice['location'] === $location->value
        ));
    }
}