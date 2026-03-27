<?php

declare(strict_types=1);

namespace Municipio\StyleguideCss\AddCustomizePropertiesToComponents;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class AddCustomizerPropertiesToComponentsTest extends TestCase
{
    #[TestDox('addHooks registers component data filter when editor access is allowed')]
    public function testAddHooksRegistersFilterWhenEditorAccessIsAllowed(): void
    {
        $wpService = new FakeWpService([
            'isAdmin' => true,
            'currentUserCan' => true,
            'addFilter' => true,
        ]);

        $sut = new AddCustomizerPropertiesToComponents($wpService);

        $sut->addHooks();

        $this->assertArrayHasKey('addFilter', $wpService->methodCalls);
        $this->assertSame('ComponentLibrary/Component/Data', $wpService->methodCalls['addFilter'][0][0]);
        $this->assertSame(10, $wpService->methodCalls['addFilter'][0][2]);
        $this->assertSame(2, $wpService->methodCalls['addFilter'][0][3]);
    }

    #[TestDox('addHooks does not register component data filter when editor access is denied')]
    public function testAddHooksDoesNotRegisterFilterWhenEditorAccessIsDenied(): void
    {
        $wpService = new FakeWpService([
            'isAdmin' => false,
            'currentUserCan' => true,
            'addFilter' => true,
        ]);

        $sut = new AddCustomizerPropertiesToComponents($wpService);

        $sut->addHooks();

        $this->assertArrayNotHasKey('addFilter', $wpService->methodCalls);
    }

    #[TestDox('addCustomizerProperties supports callback invocation with one argument')]
    public function testAddCustomizerPropertiesSupportsOneArgumentInvocation(): void
    {
        $wpService = new FakeWpService();
        $sut = new AddCustomizerPropertiesToComponents($wpService);

        $result = $sut->addCustomizerProperties([]);

        $this->assertArrayHasKey('attributeList', $result);
        $this->assertArrayHasKey('data-customizer', $result['attributeList']);

        $customizerData = json_decode($result['attributeList']['data-customizer'], true);
        $this->assertSame('string', $customizerData['type']);
        $this->assertSame('test', $customizerData['default']);
    }
}
