<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;

class AsyncConfigBuilderTest extends TestCase
{
    public function testBuildReturnsAllSetAttributes(): void
    {
        $builder = (new AsyncConfigBuilder())
            ->setQueryVarsPrefix('archive_')
            ->setId('archive_id')
            ->setPostType('post')
            ->setDateSource('post_date')
            ->setDateFormat('date-time')
            ->setNumberOfColumns(3)
            ->setPostsPerPage(10)
            ->setPaginationEnabled(true)
            ->setAsyncId('async_123')
            ->setIsAsync(true);

        $result = $builder->build();

        $this->assertEquals([
            'queryVarsPrefix' => 'archive_',
            'id' => 'archive_id',
            'postType' => 'post',
            'dateSource' => 'post_date',
            'dateFormat' => 'date-time',
            'numberOfColumns' => 3,
            'postsPerPage' => 10,
            'paginationEnabled' => true,
            'asyncId' => 'async_123',
            'isAsync' => true,
        ], $result);
    }

    public function testBuilderUsesDefaultValues(): void
    {
        $builder = new AsyncConfigBuilder();
        $result = $builder->build();

        $this->assertEquals([
            'queryVarsPrefix' => null,
            'id' => null,
            'postType' => null,
            'dateSource' => 'post_date',
            'dateFormat' => 'date-time',
            'numberOfColumns' => 1,
            'postsPerPage' => 10,
            'paginationEnabled' => true,
            'asyncId' => null,
            'isAsync' => false,
        ], $result);
    }

    public function testResetRestoresDefaultValues(): void
    {
        $builder = (new AsyncConfigBuilder())
            ->setQueryVarsPrefix('archive_')
            ->setId('archive_id')
            ->setPostType('post')
            ->setNumberOfColumns(3)
            ->setIsAsync(true);

        $builder->reset();
        $result = $builder->build();

        $this->assertEquals([
            'queryVarsPrefix' => null,
            'id' => null,
            'postType' => null,
            'dateSource' => 'post_date',
            'dateFormat' => 'date-time',
            'numberOfColumns' => 1,
            'postsPerPage' => 10,
            'paginationEnabled' => true,
            'asyncId' => null,
            'isAsync' => false,
        ], $result);
    }

    public function testBuilderImplementsFluentInterface(): void
    {
        $builder = new AsyncConfigBuilder();
        $result = $builder->setId('test');

        $this->assertInstanceOf(AsyncConfigBuilderInterface::class, $result);
        $this->assertSame($builder, $result);
    }
}
