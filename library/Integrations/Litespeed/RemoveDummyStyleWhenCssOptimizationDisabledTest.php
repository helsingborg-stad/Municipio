<?php

declare(strict_types=1);

namespace Municipio\Integrations\Litespeed;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetOption;
use WpService\Contracts\WpDequeueStyle;
use WpService\Contracts\WpDeregisterStyle;

class RemoveDummyStyleWhenCssOptimizationDisabledTest extends TestCase
{
    #[TestDox('registers after LiteSpeed Cache has enqueued its stylesheet')]
    public function testRegistersLateEnqueueHook(): void
    {
        $wpService = $this->createWpService([]);
        $sut = new RemoveDummyStyleWhenCssOptimizationDisabled($wpService);

        $sut->addHooks();

        static::assertSame('wp_enqueue_scripts', $wpService->actions[0]['hookName']);
        static::assertSame(PHP_INT_MAX, $wpService->actions[0]['priority']);
    }

    #[TestDox('removes the dummy stylesheet when CSS optimization is disabled')]
    public function testRemovesDummyStyleWhenCssOptimizationIsDisabled(): void
    {
        $wpService = $this->createWpService([
            'optm-css_min' => false,
            'optm-css_comb' => false,
            'optm-css_async' => false,
        ]);
        $sut = new RemoveDummyStyleWhenCssOptimizationDisabled($wpService);

        $sut->removeStyleWhenCssOptimizationIsDisabled();

        static::assertSame(['litespeed-cache-dummy'], $wpService->dequeuedStyles);
        static::assertSame(['litespeed-cache-dummy'], $wpService->deregisteredStyles);
    }

    #[TestDox('keeps the dummy stylesheet when a CSS optimization is enabled')]
    public function testKeepsDummyStyleWhenCssOptimizationIsEnabled(): void
    {
        $wpService = $this->createWpService(['optm-css_async' => true]);
        $sut = new RemoveDummyStyleWhenCssOptimizationDisabled($wpService);

        $sut->removeStyleWhenCssOptimizationIsDisabled();

        static::assertSame([], $wpService->dequeuedStyles);
        static::assertSame([], $wpService->deregisteredStyles);
    }

    /** @param array<string, mixed> $options */
    private function createWpService(array $options): object
    {
        return new class ($options) implements AddAction, GetOption, WpDequeueStyle, WpDeregisterStyle {
            /** @var list<array{hookName: string, callback: callable, priority: int, acceptedArgs: int}> */
            public array $actions = [];

            /** @var list<string> */
            public array $dequeuedStyles = [];

            /** @var list<string> */
            public array $deregisteredStyles = [];

            /** @param array<string, mixed> $options */
            public function __construct(private array $options) {}

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->actions[] = compact('hookName', 'callback', 'priority', 'acceptedArgs');

                return true;
            }

            public function getOption(string $option, mixed $defaultValue = false): mixed
            {
                return $option === 'litespeed-cache-conf' ? $this->options : $defaultValue;
            }

            public function wpDequeueStyle(string $handle): void
            {
                $this->dequeuedStyles[] = $handle;
            }

            public function wpDeregisterStyle(string $handle): void
            {
                $this->deregisteredStyles[] = $handle;
            }
        };
    }
}
