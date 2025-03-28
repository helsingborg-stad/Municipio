<?php

namespace Municipio\Content\PostFilters;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Query;
use WpService\Implementations\FakeWpService;
use WpService\WpService;

class RemoveExpiredEventsFromMainArchiveQueryTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $sut = new RemoveExpiredEventsFromMainArchiveQuery(new FakeWpService(), $this->getTryGetSchemaTypeFromPostTypeMock());
        $this->assertInstanceOf(RemoveExpiredEventsFromMainArchiveQuery::class, $sut);
    }

    /**
     * @testdox addHooks() adds hook to pre_get_posts
     */
    public function testPreGetPostsHookIsUsedToModifyTheMainQuery()
    {
        $wpService = new FakeWpService(['addAction' => true]);

        $sut = new RemoveExpiredEventsFromMainArchiveQuery($wpService, $this->getTryGetSchemaTypeFromPostTypeMock());
        $sut->addHooks();

        $this->assertEquals('pre_get_posts', $wpService->methodCalls['addAction'][0][0]);
    }

    /**
     * @testdox does nothing if the query is not the main query
     */
    public function testRemoveExpiredEventsDoesNothingIfNotMainQuery()
    {
        $query = $this->getWpQueryMock();
        $query->method('is_main_query')->willReturn(false);
        $querySnapshot = clone $query;

        $sut = new RemoveExpiredEventsFromMainArchiveQuery(new FakeWpService(), $this->getTryGetSchemaTypeFromPostTypeMock());
        $sut->removeExpiredEventsFromMainArchiveQuery($query);

        $this->assertEquals($querySnapshot, $query);
    }

    /**
     * @testdox does nothing if the query is not an archive query
     */
    public function testRemoveExpiredEventsDoesNothingIfNotArchiveQuery()
    {
        $query = $this->getWpQueryMock();
        $query->method('is_main_query')->willReturn(true);
        $query->method('is_archive')->willReturn(false);
        $querySnapshot = clone $query;

        $sut = new RemoveExpiredEventsFromMainArchiveQuery(new FakeWpService(), $this->getTryGetSchemaTypeFromPostTypeMock());
        $sut->removeExpiredEventsFromMainArchiveQuery($query);

        $this->assertEquals($querySnapshot, $query);
    }

    /**
     * @testdox does nothing if the post type is not connected to the Event schema type
     */
    public function testRemoveExpiredEventsDoesNothingIfPostTypeIsNotEvent()
    {
        $query = $this->getWpQueryMock();
        $query->method('is_main_query')->willReturn(true);
        $query->method('is_archive')->willReturn(true);
        $query->method('get')->with('post_type')->willReturn('not-event');
        $querySnapshot = clone $query;

        $tryGetSchemaTypeFromPostType = $this->getTryGetSchemaTypeFromPostTypeMock();
        $tryGetSchemaTypeFromPostType->method('tryGetSchemaTypeFromPostType')->willReturn(null);

        $sut = new RemoveExpiredEventsFromMainArchiveQuery(new FakeWpService(), $tryGetSchemaTypeFromPostType);
        $sut->removeExpiredEventsFromMainArchiveQuery($query);

        $this->assertEquals($querySnapshot, $query);
    }

    /**
     * @testdox filters out expired events
     */
    public function testRemoveExpiredEventsFiltersOutExpiredEvents()
    {
        $querySetCalls                = [];
        $query                        = $this->getWpQueryMock();
        $tryGetSchemaTypeFromPostType = $this->getTryGetSchemaTypeFromPostTypeMock();

        $query->method('is_main_query')->willReturn(true);
        $query->method('is_archive')->willReturn(true);
        $query->method('get')->willReturnCallback(function ($key) {
            return match ($key) {
                'post_type' => 'event',
                'meta_query' => [],
                default => null,
            };
        });

        $query->method('set')->willReturnCallback(function ($key, $value) use (&$querySetCalls) {
            $querySetCalls[$key] = $value;
        });

        $tryGetSchemaTypeFromPostType->method('tryGetSchemaTypeFromPostType')->willReturn('Event');

        $sut = new RemoveExpiredEventsFromMainArchiveQuery(new FakeWpService(), $tryGetSchemaTypeFromPostType);
        $sut->removeExpiredEventsFromMainArchiveQuery($query);

        $this->assertArrayHasKey('meta_query', $querySetCalls);
        $this->assertEquals('endDate', $querySetCalls['meta_query'][0]['key']);
        $this->assertEquals('>=', $querySetCalls['meta_query'][0]['compare']);
        $this->assertEquals('DATETIME', $querySetCalls['meta_query'][0]['type']);
    }

    private function getWpQueryMock(): WP_Query|MockObject
    {
        return $this->createMock(WP_Query::class);
    }

    private function getTryGetSchemaTypeFromPostTypeMock(): TryGetSchemaTypeFromPostType|MockObject
    {
        return $this->createMock(TryGetSchemaTypeFromPostType::class);
    }
}
