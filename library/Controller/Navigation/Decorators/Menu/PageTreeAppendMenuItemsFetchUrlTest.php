<?php

declare(strict_types=1);

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfig;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PageTreeAppendMenuItemsFetchUrlTest extends TestCase
{
    #[TestDox('getMenu() applies the page tree fetch URL filter to generated fetch URLs')]
    public function testGetMenuAppliesPageTreeFetchUrlFilter(): void
    {
        $inner = new class implements MenuInterface {
            public function getMenu(): array
            {
                return [
                    'items' => [
                        [
                            'id' => 26,
                        ],
                    ],
                ];
            }

            public function getConfig(): MenuConfigInterface
            {
                return new MenuConfig('mobile');
            }
        };

        $wpService = new FakeWpService([
            'getHomeUrl' => 'http://localhost:8080/hbgcom',
            'escUrl'     => static fn (string $url): string => $url,
            'applyFilters' => static fn (string $hookName, string $value): string => $hookName === 'Municipio/homeUrl'
                ? $value
                : $value . '&lang=sv',
        ]);

        $sut = new PageTreeAppendMenuItemsFetchUrl($inner, $wpService);
        $menu = $sut->getMenu();

        static::assertSame(
            'http://localhost:8080/hbgcom/wp-json/municipio/v1/navigation/children/render?pageId=26&depth=2&viewPath=partials.navigation.mobile&identifier=mobile&lang=sv',
            $menu['items'][0]['attributeList']['data-fetch-url']
        );
    }
}