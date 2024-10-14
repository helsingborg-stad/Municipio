<?php

namespace Municipio\AcfFieldContentModifiers;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class RegistrarTest extends TestCase
{
    /**
     * @testdox Adds prepare_field filter for field key.
     */
    public function testRegisterModifier()
    {
        $modifier  = $this->getFakeModifier();
        $wpService = new FakeWpService(['addFilter' => true]);

        $registrar = new Registrar($wpService);
        $registrar->registerModifier('field_123', $modifier);

        $this->assertEquals('acf/prepare_field/key=field_123', $wpService->methodCalls['addFilter'][0][0]);
    }

    /**
     * @testdox Filter callback applies modifier to field.
     */
    public function testApplyModifier()
    {
        $modifier  = $this->getFakeModifier();
        $wpService = new FakeWpService(['addFilter' => true, 'getPostType' => 'post']);

        $registrar = new Registrar($wpService);
        $registrar->registerModifier('field_123', $modifier);
        $callback = $wpService->methodCalls['addFilter'][0][1];

        call_user_func($callback, ['key' => 'field_123']);
        $this->assertEquals(1, $modifier->modifyFieldContentCalls);
    }

    /**
     * @testdox Does not apply modifier when on field group edit screen.
     */
    public function testApplyModifierOnFieldGroupEditScreen()
    {
        $modifier  = $this->getFakeModifier();
        $wpService = new FakeWpService(['getPostType' => 'acf-field-group', 'addFilter' => true]);

        $registrar = new Registrar($wpService);
        $registrar->registerModifier('field_123', $modifier);
        $callback = $wpService->methodCalls['addFilter'][0][1];

        call_user_func($callback, ['key' => 'field_123']);
        $this->assertEquals(0, $modifier->modifyFieldContentCalls);
    }

    private function getFakeModifier(): AcfFieldContentModifierInterface
    {
        return new class implements AcfFieldContentModifierInterface {
            public int $modifyFieldContentCalls = 0;
            public function modifyFieldContent(array $field): array
            {
                $this->modifyFieldContentCalls++;
                return $field;
            }
        };
    }
}
