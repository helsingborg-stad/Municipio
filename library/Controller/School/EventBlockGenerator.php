<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use Municipio\PostsList\Block\PostsListEventsBlockRender\PostsListEventsBlockRender;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Preschool;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use WpService\Contracts\GetPosts;

class EventBlockGenerator
{
    public function __construct(
        private ElementarySchool|Preschool $school,
        private PostsListEventsBlockRender $postsListEventsBlockRender,
        private GetPosts $wpService,
        private SchemaToPostTypeResolverInterface $postTypeResolver,
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
        $eventPostTypes = $this->postTypeResolver->resolve('Event');

        if (count($eventPostTypes) === 0) {
            return [];
        }

        $eventIds = array_map(
            static fn($event) => $event['@id'],
            $this->school->getProperty('event') ?? [],
        );

        if (count($eventIds) === 0) {
            return [];
        }

        // Find post ids by event ids. These are stored on posts in meta table under key "originId".
        // Only include events with an upcoming occasion, matching the date filter applied by the
        // posts list (GetPostsConfigWithPassedSchemaEventsFilteredOut). Otherwise the section would
        // render with an empty "no posts found" notice.
        return $this->wpService->getPosts([
            'post_type' => $eventPostTypes,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'originId',
                    'value' => $eventIds,
                    'compare' => 'IN',
                ],
                [
                    'key' => 'startDate',
                    'value' => date('Y-m-d 00:00:00'),
                    'compare' => '>=',
                    'type' => 'DATETIME',
                ],
            ],
            'fields' => 'ids',
            'posts_per_page' => -1,
        ]);
    }
}
