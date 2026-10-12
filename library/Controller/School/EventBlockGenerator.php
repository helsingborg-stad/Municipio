<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use Municipio\PostsList\Block\PostsListEventsBlockRender\PostsListEventsBlockRender;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Preschool;
use WpService\Contracts\GetPosts;

class EventBlockGenerator
{
    public function __construct(
        private ElementarySchool|Preschool $school,
        private PostsListEventsBlockRender $postsListEventsBlockRender,
        private GetPosts $wpService,
    ) {}

    public function generate(): mixed
    {
        return $this->renderEventsBlock();
    }

    /**
     * Render the event posts list for the preschool view.
     *
     * @return string Rendered block HTML.
     */
    private function renderEventsBlock(): ?string
    {
        $eventPostIds = $this->getEventPostIdsFromEventsInSchema();

        if (count($eventPostIds) === 0) {
            return null;
        }

        return $this->postsListEventsBlockRender->render([
            'numberOfColumns' => 2,
            'paginationEnabled' => false,
            'postsPerPage' => 2,
            'includedPostIds' => $eventPostIds,
        ]);
    }

    private function getEventPostIdsFromEventsInSchema(): array
    {
        $eventIds = array_map(
            fn($event) => $event['@id'],
            $this->school->getProperty('event') ?? [],
        );

        if (count($eventIds) === 0) {
            return [];
        }

        // Find post ids by event ids. These are stored on posts in meta table under key "originId"
        return $this->wpService->getPosts([
            'post_type' => 'event',
            'meta_query' => [
                [
                    'key' => 'originId',
                    'value' => $eventIds,
                    'compare' => 'IN',
                ],
            ],
            'fields' => 'ids',
            'posts_per_page' => -1,
        ]);
    }
}
