<?php

namespace Municipio\Customizer\Applicators\Types;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ApplicatorRuntimeCacheTest extends TestCase
{
    #[TestDox('component rule matches are reused without caching component-specific data')]
    public function testComponentRuleMatchesAreReusedWithoutCachingComponentData(): void
    {
        $applicator = new Component(new FakeWpService(['addFilter' => true]));
        $applicator->applyData([
            [
                'contexts' => [
                    ['operator' => '==', 'context' => 'component.button'],
                ],
                'data' => [
                    'classList' => 'from-customizer',
                    'size' => 'medium',
                ],
            ],
        ]);

        $first = $applicator->applyDataFilterFunction([
            'context' => ['component.button'],
            'classList' => ['first-instance'],
            'label' => 'First',
        ]);
        $second = $applicator->applyDataFilterFunction([
            'context' => ['component.button'],
            'classList' => ['second-instance'],
            'label' => 'Second',
        ]);

        $this->assertSame(['first-instance', 'from-customizer'], $first['classList']);
        $this->assertSame('First', $first['label']);
        $this->assertSame(['second-instance', 'from-customizer'], $second['classList']);
        $this->assertSame('Second', $second['label']);
        $this->assertSame('medium', $second['size']);
        $this->assertCount(1, $this->getPrivateArray($applicator, 'matchingFiltersCache'));
    }

    #[TestDox('component rule cache is cleared when applicator data changes')]
    public function testComponentRuleCacheIsClearedWhenApplicatorDataChanges(): void
    {
        $applicator = new Component(new FakeWpService(['addFilter' => true]));
        $applicator->applyData([$this->componentRule('old')]);
        $applicator->applyDataFilterFunction(['context' => ['component.button']]);

        $applicator->applyData([$this->componentRule('new')]);
        $result = $applicator->applyDataFilterFunction(['context' => ['component.button']]);

        $this->assertSame('new', $result['value']);
        $this->assertCount(1, $this->getPrivateArray($applicator, 'matchingFiltersCache'));
    }

    #[TestDox('modifier results are reused by context and cleared when applicator data changes')]
    public function testModifierResultsAreReusedAndInvalidated(): void
    {
        $applicator = new Modifier(new FakeWpService(['addFilter' => true]));
        $applicator->applyData([$this->modifierRule('old')]);

        $this->assertSame(['old'], $applicator->applyDataFilterFunction([], ['component.button']));
        $this->assertSame(['old'], $applicator->applyDataFilterFunction([], ['component.button']));
        $this->assertCount(1, $this->getPrivateArray($applicator, 'resolvedModifiersCache'));

        $applicator->applyData([$this->modifierRule('new')]);

        $this->assertSame(['new'], $applicator->applyDataFilterFunction([], ['component.button']));
        $this->assertCount(1, $this->getPrivateArray($applicator, 'resolvedModifiersCache'));
    }

    private function componentRule(string $value): array
    {
        return [
            'contexts' => [
                ['operator' => '==', 'context' => 'component.button'],
            ],
            'data' => ['value' => $value],
        ];
    }

    private function modifierRule(string $value): array
    {
        return [
            'contexts' => [
                ['operator' => '==', 'context' => 'component.button'],
            ],
            'value' => $value,
        ];
    }

    private function getPrivateArray(object $object, string $property): array
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }
}
