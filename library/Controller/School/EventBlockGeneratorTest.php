<?php

declare(strict_types=1);

namespace Municipio\Controller\School;

use Municipio\PostsList\Block\PostsListEventsBlockRender\PostsListEventsBlockRender;
use Municipio\Schema\Schema;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetPosts;

class EventBlockGeneratorTest extends TestCase
{
    #[TestDox('returns null when no upcoming event posts are found')]
    public function testReturnsNullWhenNoUpcomingEvents(): void
    {
        $wpService = $this->createMock(GetPosts::class);
        $wpService->method('getPosts')->willReturn([]);

        $renderer = $this->createMock(PostsListEventsBlockRender::class);
        $renderer->expects($this->never())->method('render');

        $generator = new EventBlockGenerator($this->getSchool(), $renderer, $wpService, $this->getResolver());

        $this->assertNull($generator->generate());
    }

    #[TestDox('only queries event posts with a start date today or later')]
    public function testQueriesOnlyUpcomingEvents(): void
    {
        $wpService = $this->createMock(GetPosts::class);
        $wpService->expects($this->once())->method('getPosts')->with($this->callback(function (array $args) {
            $dateClause = $args['meta_query'][1] ?? [];
            return $args['meta_query']['relation'] === 'AND'
                && $dateClause['key'] === 'startDate'
                && $dateClause['compare'] === '>='
                && $dateClause['value'] === date('Y-m-d 00:00:00');
        }))->willReturn([123]);

        $renderer = $this->createMock(PostsListEventsBlockRender::class);
        $renderer->method('render')->willReturn('<div>events</div>');

        $generator = new EventBlockGenerator($this->getSchool(), $renderer, $wpService, $this->getResolver());

        $this->assertSame('<div>events</div>', $generator->generate());
    }

    private function getSchool()
    {
        return Schema::preschool()->event([Schema::event()->setProperty('@id', 'event-1')]);
    }

    private function getResolver(): SchemaToPostTypeResolverInterface
    {
        $resolver = $this->createMock(SchemaToPostTypeResolverInterface::class);
        $resolver->method('resolve')->willReturn(['event']);
        return $resolver;
    }
}
