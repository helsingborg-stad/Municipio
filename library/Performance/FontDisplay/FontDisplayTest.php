<?php

declare(strict_types=1);

namespace Municipio\Performance\FontDisplay;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class FontDisplayTest extends TestCase
{
    #[TestDox('registers a Global Styles filter')]
    public function testAddHooksRegistersGlobalStylesFilter(): void
    {
        $wpService = new class implements AddFilter {
            public array $filters = [];

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->filters[] = compact('hookName', 'callback', 'priority', 'acceptedArgs');
                return true;
            }
        };

        (new FontDisplay($wpService))->addHooks();

        static::assertSame('wp_theme_json_data_user', $wpService->filters[0]['hookName']);
    }

    #[TestDox('uses swap for fallback and unspecified Font Library faces')]
    public function testUsesSwapForDefaultFontLibraryFaces(): void
    {
        $data = new class([
            'version' => 3,
            'settings' => [
                'typography' => [
                    'fontFamilies' => [
                        'custom' => [[
                            'fontFace' => [
                                ['fontFamily' => 'Example', 'src' => ['example.woff2']],
                                ['fontFamily' => 'Example', 'fontDisplay' => 'fallback', 'src' => ['example-bold.woff2']],
                                ['fontFamily' => 'Example', 'fontDisplay' => 'optional', 'src' => ['example-italic.woff2']],
                            ],
                        ]],
                    ],
                ],
            ],
        ]) {
            public array $updates = [];

            public function __construct(private array $data)
            {
            }

            public function get_data(): array
            {
                return $this->data;
            }

            public function update_with(array $data): self
            {
                $this->updates[] = $data;
                return $this;
            }
        };

        $result = (new FontDisplay($this->createMock(AddFilter::class)))->useSwapForFontLibraryFaces($data);

        static::assertSame($data, $result);
        static::assertSame('swap', $data->updates[0]['settings']['typography']['fontFamilies']['custom'][0]['fontFace'][0]['fontDisplay']);
        static::assertSame('swap', $data->updates[0]['settings']['typography']['fontFamilies']['custom'][0]['fontFace'][1]['fontDisplay']);
        static::assertSame('optional', $data->updates[0]['settings']['typography']['fontFamilies']['custom'][0]['fontFace'][2]['fontDisplay']);
    }

    #[TestDox('leaves Global Styles without default Font Library faces unchanged')]
    public function testLeavesNonDefaultFontLibraryFacesUnchanged(): void
    {
        $data = new class([
            'version' => 3,
            'settings' => [
                'typography' => [
                    'fontFamilies' => [
                        'custom' => [[
                            'fontFace' => [[
                                'fontFamily' => 'Example',
                                'fontDisplay' => 'optional',
                                'src' => ['example.woff2'],
                            ]],
                        ]],
                    ],
                ],
            ],
        ]) {
            public int $updates = 0;

            public function __construct(private array $data)
            {
            }

            public function get_data(): array
            {
                return $this->data;
            }

            public function update_with(array $data): self
            {
                $this->updates++;
                return $this;
            }
        };

        $result = (new FontDisplay($this->createMock(AddFilter::class)))->useSwapForFontLibraryFaces($data);

        static::assertSame($data, $result);
        static::assertSame(0, $data->updates);
    }
}
