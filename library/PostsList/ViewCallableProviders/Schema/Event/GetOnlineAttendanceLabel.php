<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders\Schema\Event;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use Municipio\Schema\EventAttendanceModeEnumeration;
use WpService\Contracts\__;

/**
 * Class GetOnlineAttendanceLabel
 *
 * Provides a label for event posts that take place online.
 * Uses the same rule as the single event view: only events with
 * eventAttendanceMode OnlineEventAttendanceMode are considered online.
 */
class GetOnlineAttendanceLabel implements ViewCallableProviderInterface
{
    public function __construct(
        private __ $wpService,
    ) {}

    /**
     * Get a callable that returns the online label for an event post, or null if the event is not online.
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return fn(PostObjectInterface $post): ?string => $this->getLabel($post);
    }

    /**
     * Get the online label for the given post.
     */
    private function getLabel(PostObjectInterface $post): ?string
    {
        if ($post->getSchema()->getProperty('eventAttendanceMode') !== EventAttendanceModeEnumeration::OnlineEventAttendanceMode) {
            return null;
        }

        return $this->wpService->__('Takes place online', 'municipio');
    }
}
