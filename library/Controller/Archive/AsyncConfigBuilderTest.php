<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;

class AsyncConfigBuilderTest extends TestCase
{
    public function testBuildReturnsAllSetAttributes()
    {
        $builder = (new AsyncConfigBuilder())
            ->setQueryVarsPrefix('archive_')
            ->setId('archive_id')
            ->setPostType('post')
            ->setDateSource('post_date')
            ->setDateFormat('date-time')
            ->setNumberOfColumns(3)
            ->setPostsPerPage(10)
            ->setPaginationEnabled(true);

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
        ], $result);
    }
}
