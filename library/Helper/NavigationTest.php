<?php

namespace Municipio\Helper;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class NavigationTest extends TestCase
{
    #[TestDox('resolveArchiveBreadcrumbLabel() prefers the current page title on archive pages')]
    public function testResolveArchiveBreadcrumbLabelPrefersCurrentPageTitleOnArchivePages(): void
    {
        $navigation = new class () extends Navigation {
            protected function isArchiveContext(): bool
            {
                return true;
            }

            protected function getPageTitle(int $pageId): string
            {
                return $pageId === 10748 ? 'Talent & Research' : '';
            }

            public function callResolveArchiveBreadcrumbLabel(int $pageId, string $fallbackLabel): string
            {
                return $this->resolveArchiveBreadcrumbLabel($pageId, $fallbackLabel);
            }
        };

        $result = $navigation->callResolveArchiveBreadcrumbLabel(10748, 'Akademi, utbildning & forskning');

        $this->assertSame('Talent & Research', $result);
    }

    #[TestDox('resolveArchiveBreadcrumbLabel() falls back to the archive object label outside archive pages')]
    public function testResolveArchiveBreadcrumbLabelFallsBackOutsideArchivePages(): void
    {
        $navigation = new class () extends Navigation {
            protected function isArchiveContext(): bool
            {
                return false;
            }

            protected function getPageTitle(int $pageId): string
            {
                return 'Talent & Research';
            }

            public function callResolveArchiveBreadcrumbLabel(int $pageId, string $fallbackLabel): string
            {
                return $this->resolveArchiveBreadcrumbLabel($pageId, $fallbackLabel);
            }
        };

        $result = $navigation->callResolveArchiveBreadcrumbLabel(10748, 'Akademi, utbildning & forskning');

        $this->assertSame('Akademi, utbildning & forskning', $result);
    }
}