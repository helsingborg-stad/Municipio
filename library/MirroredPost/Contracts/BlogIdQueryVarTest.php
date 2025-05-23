<?php

namespace Municipio\MirroredPost\Contracts;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class BlogIdQueryVarTest extends TestCase
{
    public function testBlogIdQueryVarConstant()
    {
        // Ensure that the constant is defined.
        $this->assertIsString(BlogIdQueryVar::BLOG_ID_QUERY_VAR);
    }

    /**
     * @testdox addHooks method appends the query variable to the list of query variables.
     */
    public function testAddHooks()
    {
        $wpService      = new FakeWpService(['addFilter' => true]);
        $blogIdQueryVar = new BlogIdQueryVar($wpService);

        $blogIdQueryVar->addHooks();

        $this->assertEquals('query_vars', $wpService->methodCalls['addFilter'][0][0]);
    }

    /**
     * @testdox appendToQueryVars method appends the query variable to the list of query variables.
     */
    public function testAppendToQueryVars()
    {
        $wpService      = new FakeWpService(['addFilter' => true]);
        $blogIdQueryVar = new BlogIdQueryVar($wpService);

        $queryVars = $blogIdQueryVar->appendToQueryVars([]);

        $this->assertContains(BlogIdQueryVar::BLOG_ID_QUERY_VAR, $queryVars);
    }
}
