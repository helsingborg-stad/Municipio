<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Action;
use Municipio\Schema\Contracts\ActionContract;
use Municipio\Schema\Event;
use Municipio\Schema\EventAttendanceModeEnumeration;
use Municipio\Schema\JoinAction;
use WpService\Contracts\__;
use WpService\Contracts\EscUrlRaw;

/**
 * Maps online attendance information for events held online.
 *
 * Returns null unless eventAttendanceMode is OnlineEventAttendanceMode.
 * The join button is only included if the event has a JoinAction with a valid http(s) url.
 */
class MapOnlineAttendance implements EventDataMapperInterface
{
    private const JOIN_ACTION_TYPE = 'JoinAction';

    /**
     * Constructor
     */
    public function __construct(private __&EscUrlRaw $wpService)
    {
    }

    /**
     * @return array{notice: string, button: ?array{text: string, href: string, ariaLabel: string}}|null
     */
    public function map(Event $event): ?array
    {
        if ($event->getProperty('eventAttendanceMode') !== EventAttendanceModeEnumeration::OnlineEventAttendanceMode) {
            return null;
        }

        return [
            'notice' => $this->wpService->__('The event has no physical location and takes place online.', 'municipio'),
            'button' => $this->getButton($event),
        ];
    }

    /**
     * Get join button data from the first JoinAction with a valid url.
     */
    private function getButton(Event $event): ?array
    {
        $actions = EnsureArrayOf::ensureArrayOf($event->getProperty('potentialAction'), ActionContract::class);

        foreach ($actions as $action) {
            if (!$this->isJoinAction($action)) {
                continue;
            }

            $url = $this->sanitizeUrl($action->getProperty('url'));

            if ($url === null) {
                continue;
            }

            $text = $this->getButtonText($action);

            return [
                'text'      => $text,
                'href'      => $url,
                'ariaLabel' => sprintf(
                    '%s, %s',
                    $text,
                    $this->wpService->__('The link opens in a new window', 'municipio')
                ),
            ];
        }

        return null;
    }

    /**
     * A JoinAction can be either a JoinAction object or an Action with type "JoinAction".
     */
    private function isJoinAction(ActionContract $action): bool
    {
        return $action instanceof JoinAction
            || ($action instanceof Action && $action->getProperty('type') === self::JOIN_ACTION_TYPE);
    }

    /**
     * Use the action description as button text, with a default fallback.
     */
    private function getButtonText(ActionContract $action): string
    {
        $description = $action->getProperty('description');

        if (is_string($description) && trim($description) !== '') {
            return trim($description);
        }

        return $this->wpService->__('Join the online event', 'municipio');
    }

    /**
     * Only allow valid http(s) urls.
     */
    private function sanitizeUrl(mixed $url): ?string
    {
        if (!is_string($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $url = $this->wpService->escUrlRaw($url, ['http', 'https']);

        return $url !== '' ? $url : null;
    }
}
