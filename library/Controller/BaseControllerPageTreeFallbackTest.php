<?php

declare(strict_types=1);

namespace Municipio\Controller;

use PHPUnit\Framework\TestCase;

final class BaseControllerPageTreeFallbackTest extends TestCase
{
    public function testUsesCustomizerDefaultWhenNoFallbackSettingHasBeenSaved(): void
    {
        $defaultMenus = ['primary', 'secondary', 'mobile'];

        static::assertTrue($this->resolveFallback('primary', $defaultMenus));
        static::assertTrue($this->resolveFallback('secondary', $defaultMenus));
        static::assertTrue($this->resolveFallback('mobile', $defaultMenus));
        static::assertFalse($this->resolveFallback('mega', $defaultMenus));
    }

    public function testCombinedSettingOverridesTheDefaults(): void
    {
        static::assertFalse($this->resolveFallback('primary', ['mobile']));
        static::assertFalse($this->resolveFallback('secondary', ['mobile']));
        static::assertTrue($this->resolveFallback('mobile', ['mobile']));
    }

    /**
     * @param array<int, string> $configuredMenus
     */
    private function resolveFallback(string $menuName, array $configuredMenus): bool
    {
        $controller = (new \ReflectionClass(BaseController::class))->newInstanceWithoutConstructor();
        $method     = new \ReflectionMethod(BaseController::class, 'isMenuPagetreeFallbackEnabled');

        $controller->data['customizer'] = (object) [
            'menuPagetreeFallbackMenus' => $configuredMenus,
        ];

        return $method->invoke($controller, $menuName);
    }
}
