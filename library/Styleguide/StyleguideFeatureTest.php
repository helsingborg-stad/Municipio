<?php

namespace Municipio\Styleguide;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetThemeMods;
use WpService\Implementations\NativeWpService;

class StyleguideFeatureTest extends TestCase
{
    #[TestDox('can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $feature = new StyleguideFeature(self::createWpService());
        $this->assertInstanceOf(StyleguideFeature::class, $feature);
    }

    private static function getFeatureOutput(AddAction&GetThemeMods $wpService): string
    {
        $feature = new StyleguideFeature($wpService);
        $feature->addHooks();

        $addActionCalls = $wpService->addActionCalls;
        $wpHeadAction = array_filter($addActionCalls, fn($call) => $call['hookName'] === 'wp_head');
        $callback = $wpHeadAction[0]['callback'] ?? null;

        ob_start();
        $callback();
        return ob_get_clean();
    }

    private static function createWpService(): AddAction&GetThemeMods&AddFilter
    {
        return new class extends NativeWpService {
            public array $addActionCalls = [];

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addActionCalls[] = [
                    'hookName' => $hookName,
                    'callback' => $callback,
                    'priority' => $priority,
                    'acceptedArgs' => $acceptedArgs,
                ];

                return true;
            }

            public function getThemeMods(): array
            {
                return [];
            }

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }
        };
    }
}
