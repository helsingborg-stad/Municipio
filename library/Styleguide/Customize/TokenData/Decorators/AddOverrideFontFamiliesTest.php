<?php

namespace Municipio\Styleguide\Customize\TokenData\Decorators;

use Modularity\HooksRegistrar\Hookable;
use Municipio\Styleguide\Customize\OverrideState\OverrideStateInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class AddOverrideFontFamiliesTest extends TestCase
{
    #[TestDox('adds font family from override state to options')]
    public function testAddOverrideFontFamilies(): void
    {
        $overrideStateService = self::createOverrideStateService([
            'token' => [
                '--font-family-base' => 'Custom Font Base',
                '--font-family-heading' => 'Custom Font Heading',
            ],
        ]);

        $decorator = new AddOverrideFontFamilies(self::createWpService(), $overrideStateService);

        $options = [
            ['value' => 'Default Font', 'label' => 'Default Font'],
        ];

        $result = $decorator->addOverrideFontFamilies($options);

        static::assertCount(3, $result);
        static::assertEquals('Default Font', $result[0]['value']);
        static::assertEquals('Custom Font Base', $result[1]['value']);
        static::assertEquals('Custom Font Heading', $result[2]['value']);
    }

    private static function createWpService(): AddFilter
    {
        return new class implements AddFilter {
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }
        };
    }

    private static function createOverrideStateService(array $overrideState): OverrideStateInterface
    {
        return new class($overrideState) implements OverrideStateInterface {
            private array $overrideState;

            public function __construct(array $overrideState)
            {
                $this->overrideState = $overrideState;
            }

            public function getOverrideState(): array
            {
                return $this->overrideState;
            }
        };
    }
}
