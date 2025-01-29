<?php

namespace Municipio\GlobalNotices;

use AcfService\AcfService;
use WpService\WpService;

class GetAndApplyGlobalNotices implements \Municipio\HooksRegistrar\Hookable
{
    private $noticeDataKey = 'notice';

    public function __construct(private WpService $wpService, private AcfService $acfService, private GlobalNoticesConfig $config)
    {
    }

    public function addHooks(): void
    {
      $this->wpService->addFilter('Municipio/viewData', [$this, 'filterViewData'], 10, 1);
    }

    /**
     * Get global notices.
     * 
     * @return array|null
     */
    public function getGlobalNotices(): ?array
    {
      $notices = $this->acfService->getField('global_notices', 'option') ?? [];
      $notices = array_filter($notices, [$this, 'filterByConstraints']);
      $notices = array_map([$this, 'mapGlobalNotice'], $notices);
      return $notices ?: null;
    }

    /**
     * Map global notice to a format that can be used in the view.
     * 
     * @param array $globalNotice The global notice to map.
     * 
     * @return array
     */
    public function mapGlobalNotice(array $notice): ?array
    {
      return [
        'message' => ['text' => $notice['message']],
        'location' => $notice['location'],
        'type' => $notice['type'],
        'icon' => ['name' => $notice['icon']],
        'action' => $notice['action'],
        'dismissable' => $notice['dismissable'],
        'classList' => $this->classListByLocation($notice['location'] ?? ''),
      ];
    }

    /**
     * Get class list by location.
     * 
     * @param string $location The location to get class list for.
     * 
     * @return array
     */
    public function classListByLocation(string $location): array
    {
      switch ($location) {
        case 'toast':
          return [];
        case 'banner':
          return ['u-margin--0', 'u-rounded--none'];
        case 'content':
          return [];
        default:
          return [];
      }
    }

    /**
     * Filter global notices by constraints.
     * 
     * @param array $notice The notice to filter.
     * 
     * @return bool
     */
    public function filterByConstraints(array $notice): bool
    {
      $constraints = $notice['constraints'] ?? null;
      if (!is_array($constraints)) {
        return true;
      }

      $userLoggedIn     = $this->wpService->isUserLoggedIn() ? 'loggedin' : 'loggedout';
      $currentPageType  = $this->wpService->isFrontPage() ? 'frontpage' : 'subpage';

      return in_array($userLoggedIn, $constraints) && in_array($currentPageType, $constraints);
    }

    /**
     * Get global notices by location.
     * 
     * @param string $location The location to filter by.
     * 
     * @return array
     */
    public function getGlobalNoticesByLocation(string $location): array
    {
      return array_values(array_filter(
        $this->getGlobalNotices(),
        fn($notice) => isset($notice['location']) && $notice['location'] === $location
      ));
    }

    /**
     * WordPress filter callback to inject global notices into view data.
     */
    public function filterViewData(array $data): array
    {
      foreach ($this->config->getLocations() as $location) {
        $data[$this->noticeDataKey][$location] ??= [];
        $data[$this->noticeDataKey][$location] = $this->getGlobalNoticesByLocation($location);
      }
      return $data;
    }
}