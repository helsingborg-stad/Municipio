<?php

declare(strict_types=1);

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Cache\NavigationWpCache;
use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use Municipio\Helper\GetGlobal;
use Municipio\Helper\WpService;
use PDO;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use WpService\Implementations\FakeWpService;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
#[RequiresPhpExtension('pdo_sqlite')]
class PageTreeAppendChildrenTest extends TestCase
{
    public function testBatchesChildChecksAndPreservesVisibilityAndMappedPostTypes(): void
    {
        $database = new PageTreeChildrenFakeWpdb([
            [101, 10, 'publish', 'page'],
            [102, 11, 'private', 'page'],
            [103, 12, 'publish', 'page'],
            [104, 13, 'draft', 'page'],
            [105, 0, 'publish', 'SCHOOL'],
            [106, 0, 'publish', 'article'],
            [107, 0, 'publish', 'hidden-school'],
            [108, 0, 'draft', 'event'],
        ]);
        $this->setUpNavigation($database, false, [103, 107], [
            20 => 'school', 21 => 'article', 22 => 'hidden-school', 23 => 'event', 24 => 'school', 25 => 'school',
        ]);
        $items = array_map(static fn (int $id): array => ['id' => $id], range(10, 875));
        $items[0]['id'] = '010';
        $items[] = ['id' => '025'];
        $service = new FakeWpService([
            'isPostTypeHierarchical' => static fn (string $postType): bool => $postType !== 'article',
        ]);
        $subject = new PageTreeAppendChildren($this->menu($items), $service);

        $result = $subject->getMenu();

        self::assertCount(2, $database->queries);
        $byId = array_column($result['items'], null, 'id');
        self::assertTrue($byId['010']['children']);
        self::assertFalse($byId[11]['children']);
        self::assertFalse($byId[12]['children']);
        self::assertFalse($byId[13]['children']);
        self::assertTrue($byId[20]['children']);
        self::assertFalse($byId[21]['children']);
        self::assertFalse($byId[22]['children']);
        self::assertFalse($byId[23]['children']);
        self::assertTrue($byId[24]['children']);
        self::assertTrue($byId[25]['children']);
        self::assertFalse($byId['025']['children']);
        self::assertFalse($byId[875]['children']);
    }

    public function testIncludesPrivateChildrenForLoggedInUsersAndRechecksChanges(): void
    {
        $database = new PageTreeChildrenFakeWpdb([
            [101, 10, 'private', 'page'],
            [102, 0, 'private', 'school'],
        ]);
        $this->setUpNavigation($database, true, [], [20 => 'school']);
        $subject = new PageTreeAppendChildren(
            $this->menu([['id' => 10], ['id' => 20]]),
            new FakeWpService(['isPostTypeHierarchical' => true])
        );

        $first = $subject->getMenu();
        $database->execute("UPDATE posts SET post_status = 'draft' WHERE ID IN (101, 102)");
        $second = $subject->getMenu();

        self::assertTrue($first['items'][0]['children']);
        self::assertTrue($first['items'][1]['children']);
        self::assertFalse($second['items'][0]['children']);
        self::assertFalse($second['items'][1]['children']);
        self::assertCount(4, $database->queries);
    }

    public function testSkipsCachedAndPopulatedItemsAndKeepsCurrentPageFilter(): void
    {
        $database = new PageTreeChildrenFakeWpdb([]);
        $this->setUpNavigation($database, false, [], []);
        CurrentPostId::$pageId = 50;
        $subject = new PageTreeAppendChildren(
            $this->menu([
                ['id' => 10, 'isCached' => true, 'children' => false],
                ['id' => 11, 'children' => [['id' => 12]]],
                ['id' => 50],
            ]),
            new FakeWpService([
                'getPostType' => 'page',
                'applyFilters' => [['ID' => 77, 'post_title' => 'Translated']],
            ])
        );

        $result = $subject->getMenu();

        self::assertCount(0, $database->queries);
        self::assertFalse($result['items'][0]['children']);
        self::assertSame([['id' => 12]], $result['items'][1]['children']);
        self::assertTrue($result['items'][2]['children']);
        self::assertSame(77, $result['items'][3]['id']);
        self::assertSame('Translated', $result['items'][3]['label']);
    }

    private function setUpNavigation(
        PageTreeChildrenFakeWpdb $database,
        bool $loggedIn,
        array $hiddenPostIds,
        array $pageForPostTypes
    ): void {
        (new ReflectionProperty(GetGlobal::class, 'cachedGlobals'))->setValue(null, ['wpdb' => $database]);
        CurrentPostId::$pageId = 99999;
        WpService::set(new FakeWpService(['isUserLoggedIn' => $loggedIn]));
        NavigationWpCache::setCache('hide_in_menu', $hiddenPostIds ?: [PHP_INT_MAX], false);
        NavigationWpCache::setCache('pageForPostType:v2', $pageForPostTypes, false);
    }

    private function menu(array $items): MenuInterface
    {
        return new class ($items) implements MenuInterface {
            public function __construct(private array $items)
            {
            }

            public function getMenu(): array
            {
                return ['items' => $this->items];
            }

            public function getConfig(): MenuConfigInterface
            {
                return new MenuConfig('test');
            }
        };
    }
}

class PageTreeChildrenFakeWpdb
{
    public string $posts = 'posts';
    public array $queries = [];
    private PDO $database;

    public function __construct(array $posts)
    {
        $this->database = new PDO('sqlite::memory:', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $this->database->exec('CREATE TABLE posts (ID INTEGER, post_parent INTEGER, post_status TEXT, post_type TEXT COLLATE NOCASE)');
        $statement = $this->database->prepare('INSERT INTO posts VALUES (?, ?, ?, ?)');
        foreach ($posts as $post) {
            $statement->execute($post);
        }
    }

    public function prepare(string $sql, mixed ...$arguments): string
    {
        $index = 0;
        return preg_replace_callback('/%[ds]/', function (array $match) use (&$index, $arguments): string {
            $value = $arguments[$index++];
            return $match[0] === '%d' ? (string) (int) $value : $this->database->quote((string) $value);
        }, $sql);
    }

    public function get_col(string $sql): array
    {
        $this->queries[] = $sql;
        return $this->database->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    public function get_var(string $sql): mixed
    {
        $this->queries[] = $sql;
        $result = $this->database->query($sql)->fetchColumn();
        return $result === false ? null : $result;
    }

    public function execute(string $sql): void
    {
        $this->database->exec($sql);
    }
}
