<?php

namespace Municipio\GlobalNotices;

use AcfService\AcfService;
use WpService\WpService;

class GetAndApplyGlobalNotices implements \Municipio\HooksRegistrar\Hookable, GetAndApplyGlobalNoticesInterface
{
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
    public function getGlobalNotices(): array
    {
        $notices = $this->acfService->getField('global_notices', 'option');

        if (!is_array($notices) || empty($notices)) {
            return [];
        }
        
        return array_map([$this, 'mapGlobalNotice'], array_filter($notices, [$this, 'filterByConstraints']));
    }

    /**
     * Map global notice to a format that can be used in the view.
     *
     * @param array $globalNotice The global notice to map.
     *
     * @return array
     */
    public function mapGlobalNotice(array $notice): array
    {
        $extractAction = fn($action) => !empty($action['has_action'] ?? false)
        ? ['url' => $action['target'] ?? '', 'text' => $action['label'] ?? '']
        : false;

        $extractDismissable = fn($dismissable) => !empty($dismissable['is_dismissable'] ?? false)
          ? ($dismissable['lifetime'] ?? 0)
          : false;

        $addClassByLocation = fn(string $location): array => [
          'banner' => ['u-margin--0', 'u-rounded--0']
      ][$location] ?? [];

        return [
          'message'     => ['text' => $notice['message'] ?? ''],
          'location'    => $notice['location'] ?? 'toast',
          'type'        => $notice['type'] ?? 'info',
          'icon'        => ['name' => $notice['icon'] ?? ''] ?: false,
          'action'      => $extractAction($notice['action'] ?? false),
          'dismissable' => $extractDismissable($notice['dismissable'] ?? false),
          'classList'   => $addClassByLocation($notice['location'] ?? 'toast')
        ];
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

        $userLoggedIn    = $this->wpService->isUserLoggedIn() ? 'loggedin' : 'loggedout';
        $currentPageType = $this->wpService->isFrontPage() ? 'frontpage' : 'subpage';

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
     *
     * @param array $data The view data.
     *
     * @return array
     */
    public function filterViewData(array $data): array
    {
        $noticeDataKey = $this->config->getNoticeDataKey();
        
        $data[$noticeDataKey] = $data[$noticeDataKey] ?? [];

        foreach ($this->config->getLocations() as $location) {
            $data[$noticeDataKey][$location] ??= [];
            $data[$noticeDataKey][$location]   = $this->getGlobalNoticesByLocation($location);
        }
        return $data;
    }
}
