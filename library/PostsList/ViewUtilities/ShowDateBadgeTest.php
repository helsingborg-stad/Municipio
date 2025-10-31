<?php

namespace Municipio\PostsList\ViewUtilities;

use Municipio\Helper\WpService;
use Municipio\PostObject\NullPostObject;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class FakePostObjectWithPostType extends NullPostObject
{
    public function __construct(private string $postType)
    {
    }

    public function getPostType(): string
    {
        return $this->postType;
    }
}

class ShowDateBadgeTest extends TestCase
{
    #[TestDox('returns true if any post has date-badge format')]
    public function testReturnsTrueIfAnyPostHasDateBadgeFormat(): void
    {
        WpService::set(new FakeWpService(['getThemeMod' => fn($key, $default) => $key === 'archive_post_date_format' ? 'date-badge' : $default]));
        $posts                = [new FakePostObjectWithPostType('page'), new FakePostObjectWithPostType('post')];
        $showDateBadgeUtility = new ShowDateBadge($posts);

        $this->assertTrue($showDateBadgeUtility->getCallable()());
    }

    #[TestDox('returns false if no post has date-badge format')]
    public function testReturnsFalseIfNoPostHasDateBadgeFormat(): void
    {
        WpService::set(new FakeWpService(['getThemeMod' => fn($key, $default) => $default]));
        $posts                = [new FakePostObjectWithPostType('page'), new FakePostObjectWithPostType('custom_post_type')];
        $showDateBadgeUtility = new ShowDateBadge($posts);

        $this->assertFalse($showDateBadgeUtility->getCallable()());
    }
}
