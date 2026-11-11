<?php

declare(strict_types=1);

namespace Municipio\Admin\Acf;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class PaletteColorChoicesTest extends TestCase
{
    public function testOnlyExplicitlyOptedInFieldsBecomePaletteSelectors(): void
    {
        $selector = new PaletteColorChoices(new FakeWpService(), $this->createMock(EnqueueManagerInterface::class));
        $radiusField = ['type' => 'select', 'name' => 'border_radius', 'choices' => ['sm' => 'Small']];
        static::assertSame($radiusField, $selector->populateChoices($radiusField));

        $paletteField = $selector->populateChoices([
            'type' => 'color_picker',
            'name' => 'color',
            'municipio_palette_color' => true,
            'wrapper' => ['class' => 'existing-class'],
        ]);
        static::assertSame('select', $paletteField['type']);
        static::assertSame('value', $paletteField['return_format']);
        static::assertSame(0, $paletteField['multiple']);
        static::assertSame(0, $paletteField['ajax']);
        static::assertSame(1, $paletteField['allow_null']);
        static::assertSame(__('None', 'municipio'), $paletteField['placeholder']);
        static::assertArrayHasKey('primary', $paletteField['choices']);
        static::assertSame('existing-class municipio-palette-color-selector', $paletteField['wrapper']['class']);
        static::assertSame($paletteField, $selector->populateChoices($paletteField));
    }

    public function testPreservesFieldsThatDoNotAllowEmptySelection(): void
    {
        $selector = new PaletteColorChoices(new FakeWpService(), $this->createMock(EnqueueManagerInterface::class));
        $field = $selector->populateChoices([
            'type' => 'select',
            'municipio_palette_color' => true,
            'allow_null' => 0,
        ]);

        static::assertSame(0, $field['allow_null']);
        static::assertArrayNotHasKey('', $field['choices']);
    }

    public function testConvertsBeforeAcfInitializesFieldTypeDefaults(): void
    {
        $wpService = new FakeWpService(['addFilter' => true, 'addAction' => true]);
        $selector = new PaletteColorChoices($wpService, $this->createMock(EnqueueManagerInterface::class));
        $selector->addHooks();
        static::assertContains(['acf/validate_field', [$selector, 'populateChoices'], 5], $wpService->methodCalls['addFilter']);
    }

    public function testAttachesPreviewDataToTheEnqueuedScript(): void
    {
        $enqueue = $this->createMock(EnqueueManagerInterface::class);
        $enqueue->expects(static::once())->method('add')->with('js/palette-color-choices.js', ['acf-input'])->willReturnSelf();
        $context = new class {
            public array $data = [];

            public function data(string $name, array $data): void
            {
                $this->data = [$name, $data];
            }
        };
        $enqueue->method('with')->willReturn($context);
        $selector = new PaletteColorChoices(new FakeWpService(), $enqueue);
        $selector->enqueueAssets();
        static::assertSame('MunicipioPaletteColorChoices', $context->data[0]);
        static::assertArrayHasKey('choices', $context->data[1]);
    }
}
