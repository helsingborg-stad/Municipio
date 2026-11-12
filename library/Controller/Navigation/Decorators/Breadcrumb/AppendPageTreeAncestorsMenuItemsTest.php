<?php

declare(strict_types=1);

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb {
    use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
    use Municipio\Controller\Navigation\Cache\NavigationWpCache;
    use Municipio\Controller\Navigation\Config\MenuConfigInterface;
    use Municipio\Controller\Navigation\MenuInterface;
    use Municipio\Helper\CurrentPostId;
    use PHPUnit\Framework\Attributes\TestDox;
    use PHPUnit\Framework\TestCase;
    use WpService\Contracts\IsArchive;
    use WpService\Contracts\IsFrontPage;
    use WpService\Contracts\IsPostTypeArchive;

    class AppendPageTreeAncestorsMenuItemsTest extends TestCase
    {
        private const ARCHIVE_PAGE_ID = 10;

        protected function tearDown(): void
        {
            CurrentPostId::$pageId = 0;
            $this->setPageForPostTypeCache([]);
            NavigationRuntimeCache::setCache('ancestors', ['toplevel' => [], 'notoplevel' => []]);
        }

        #[TestDox('getMenu() leaves the menu untouched on the front page')]
        public function testGetMenuLeavesMenuUntouchedOnFrontPage(): void
        {
            $sut = new AppendPageTreeAncestorsMenuItems($this->createInnerMenu(), $this->createWpService(isFrontPage: true));

            static::assertSame($this->homeItems(), $sut->getMenu()['items']);
        }

        #[TestDox('getMenu() leaves the menu untouched on archives that are not post type archives')]
        public function testGetMenuLeavesMenuUntouchedOnOtherArchives(): void
        {
            CurrentPostId::$pageId = 55; // Term id of a taxonomy archive.

            $sut = new AppendPageTreeAncestorsMenuItems($this->createInnerMenu(), $this->createWpService(isArchive: true));

            static::assertSame($this->homeItems(), $sut->getMenu()['items']);
        }

        #[TestDox('getMenu() adds the ancestors of the mapped page before the archive item on a post type archive')]
        public function testGetMenuAddsAncestorsOfMappedPageBeforeArchiveItem(): void
        {
            CurrentPostId::$pageId = self::ARCHIVE_PAGE_ID;
            $this->setPageForPostTypeCache([self::ARCHIVE_PAGE_ID => 'archive-post-type']);
            // Stack as returned by GetAncestors: [top level, mapped page, parent, grandparent].
            NavigationRuntimeCache::setCache('ancestors', [
                'toplevel' => [self::ARCHIVE_PAGE_ID => [0, self::ARCHIVE_PAGE_ID, 20, 30]],
            ]);

            $sut = new AppendPageTreeAncestorsMenuItems(
                $this->createInnerMenu([...$this->homeItems(), 'archive' => ['label' => 'Archive', 'href' => 'https://example.com/archive/', 'current' => false, 'icon' => 'chevron_right']]),
                $this->createWpService(isArchive: true, isPostTypeArchive: true),
            );

            $items = $sut->getMenu()['items'];

            // Ancestor items are keyed by post id; the mapped page itself is not repeated.
            static::assertSame(['home', 30, 20], array_slice(array_keys($items), 0, 3));
            static::assertCount(4, $items);
            static::assertSame('https://example.com/archive/', end($items)['href']);
        }

        private function setPageForPostTypeCache(array $pageForPostType): void
        {
            // Seed the runtime layer of the cache, since the persistent layer needs a running WordPress.
            $property = new \ReflectionProperty(NavigationWpCache::class, 'cache');
            $property->setValue(null, ['pageForPostType:v2' => $pageForPostType]);
        }

        private function homeItems(): array
        {
            return ['home' => ['label' => 'Home', 'href' => 'https://example.com', 'current' => false, 'icon' => 'home']];
        }

        private function createWpService(bool $isFrontPage = false, bool $isArchive = false, bool $isPostTypeArchive = false): IsFrontPage&IsArchive&IsPostTypeArchive
        {
            return new class($isFrontPage, $isArchive, $isPostTypeArchive) implements IsFrontPage, IsArchive, IsPostTypeArchive {
                public function __construct(private bool $isFrontPage, private bool $isArchive, private bool $isPostTypeArchive) {}

                public function isFrontPage(): bool
                {
                    return $this->isFrontPage;
                }

                public function isArchive(): bool
                {
                    return $this->isArchive;
                }

                public function isPostTypeArchive(string|array $postTypes = ''): bool
                {
                    return $this->isPostTypeArchive;
                }
            };
        }

        private function createInnerMenu(?array $items = null): MenuInterface
        {
            return new class($items ?? $this->homeItems()) implements MenuInterface {
                public function __construct(private array $items) {}

                public function getMenu(): array
                {
                    return ['items' => $this->items];
                }

                public function getConfig(): MenuConfigInterface
                {
                    return new class() implements MenuConfigInterface {
                        public function getIdentifier(): string
                        {
                            return 'breadcrumb';
                        }

                        public function getMenuName(): string|int
                        {
                            return '';
                        }

                        public function getRemoveSubLevels(): bool
                        {
                            return false;
                        }

                        public function getRemoveTopLevel(): bool
                        {
                            return false;
                        }

                        public function getFallbackToPageTree(): bool|int
                        {
                            return false;
                        }
                    };
                }
            };
        }
    }
}
