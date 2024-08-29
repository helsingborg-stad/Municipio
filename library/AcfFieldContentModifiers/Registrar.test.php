<?php

namespace Municipio\AcfFieldContentModifiers;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class RegistrarTest extends TestCase
{
    /**
     * @testdox Filter is added
     */
    public function testFilterIsAdded()
    {
        $wpService = new FakeWpService();
        $registrar = new Registrar($wpService);
        $modifier  = $this->getTestModifier();

        $registrar->registerModifier($this->getTestModifier());

        $this->assertEquals('acf/prepare_field/key=test', $wpService->methodCalls['addFilter'][0][0]);
        $this->assertEquals([$registrar, 'applyModifier'], $wpService->methodCalls['addFilter'][0][1]);
    }

    /**
     * @testdox Modifier is applied
     */
    public function testModifierIsAppliedWhenFilterIsCalled()
    {
        $wpService = new FakeWpService();
        $registrar = new Registrar($wpService);

        $registrar->registerModifier($this->getTestModifier());

        $this->assertEquals(['test'], $registrar->applyModifier([]));
    }


    /**
     * Test that filter is not added when on field group edit screen.
     */
    public function testFilterIsNotAddedWhenOnFieldGroupEditScreen()
    {
        $wpService = new FakeWpService(['getPostType' => 'acf-field-group']);

        $registrar = new Registrar($wpService);
        $registrar->registerModifier($this->getTestModifier());

        $this->assertEquals([], $registrar->applyModifier([]));
    }

    private function getTestModifier(): AcfFieldContentModifierInterface
    {
        return new class implements AcfFieldContentModifierInterface {
            public function modifyFieldContent(array $field): array
            {
                return ['test'];
            }

            public function getFieldKey(): string
            {
                return 'test';
            }
        };
    }
}
