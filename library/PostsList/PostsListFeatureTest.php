<?php

namespace Municipio\PostsList;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class PostsListFeatureTest extends TestCase
{
    #[TestDox('enable() adds the view path filter to ensure templates are found')]
    public function testEnableAddsViewPathFilter(): void
    {
        $wpService = new class implements AddFilter {
            public int $callCount      = 0;
            public array $receivedArgs = [];

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->callCount++;
                $this->receivedArgs = [$hookName, $callback, $priority, $acceptedArgs];
                return true;
            }
        };

        $postsListFeature = new PostsListFeature($wpService);
        $postsListFeature->enable();

        $this->assertEquals(1, $wpService->callCount);
        $this->assertEquals('Municipio/viewPaths', $wpService->receivedArgs[0]);
    }

    #[TestDox('getTemplateDir() returns the correct template directory path that contains views')]
    public function testGetTemplateDirReturnsCorrectPath(): void
    {
        $this->assertDirectoryExists(PostsListFeature::getTemplateDir());
        $this->assertGreaterThan(0, count(glob(PostsListFeature::getTemplateDir() . '/*.blade.php')));
    }
}
