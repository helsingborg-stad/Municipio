<?php

declare(strict_types=1);

namespace Municipio\Integrations\Component;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class VagueControlTextIndicatorTest extends TestCase
{
    public function testRegistersButtonAndLinkDataFilters(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        (new VagueControlTextIndicator($wpService))->addHooks();

        static::assertSame(
            ['ComponentLibrary/Component/Button/Data', 'ComponentLibrary/Component/Link/Data'],
            array_column($wpService->methodCalls['addFilter'], 0),
        );
    }

    public function testAddsIndicatorForEditorsWhenButtonOrLinkTextIsVague(): void
    {
        $wpService = new FakeWpService([
            'isUserLoggedIn' => true,
            'currentUserCan' => true,
            '__' => static fn(string $text): string => $text,
        ]);
        $indicator = new VagueControlTextIndicator($wpService);

        $button = $indicator->addButtonIndicator([
            'text' => '  Läs mer  ',
            'attributeList' => ['data-component' => 'button'],
        ]);
        $link = $indicator->addLinkIndicator([
            'slot' => '<span>Klicka här</span>',
            'href' => '/bygglov',
            'attributeList' => ['data-component' => 'link'],
        ]);

        static::assertSame('Button text is not descriptive enough', $button['attributeList']['data-a11y-error']);
        static::assertSame('button', $button['attributeList']['data-component']);
        static::assertSame('Link text is not descriptive enough', $link['attributeList']['data-a11y-error']);
        static::assertSame('link', $link['attributeList']['data-component']);
    }

    public function testDetectsOtherCommonVagueSwedishAndEnglishLabels(): void
    {
        $wpService = new FakeWpService([
            'isUserLoggedIn' => true,
            'currentUserCan' => true,
            '__' => static fn(string $text): string => $text,
        ]);
        $indicator = new VagueControlTextIndicator($wpService);

        foreach (['Här', 'Läs vidare', 'Mer', 'Click here', 'Read more', 'More'] as $label) {
            $data = $indicator->addLinkIndicator(['slot' => $label, 'href' => '/example']);
            static::assertArrayHasKey('data-a11y-error', $data['attributeList'], $label);
        }
    }

    public function testDoesNotAddIndicatorForVisitorsOrDescriptiveText(): void
    {
        $visitor = new FakeWpService([
            'isUserLoggedIn' => false,
            'currentUserCan' => false,
        ]);
        $editor = new FakeWpService([
            'isUserLoggedIn' => true,
            'currentUserCan' => true,
        ]);

        static::assertArrayNotHasKey(
            'data-a11y-error',
            (new VagueControlTextIndicator($visitor))->addButtonIndicator(['text' => 'Läs mer']),
        );
        static::assertArrayNotHasKey(
            'data-a11y-error',
            (new VagueControlTextIndicator($editor))->addLinkIndicator([
                'slot' => 'Läs mer om bygglov',
                'href' => '/bygglov',
            ]),
        );
        static::assertArrayNotHasKey(
            'data-a11y-error',
            (new VagueControlTextIndicator($editor))->addLinkIndicator(['slot' => 'Klicka här']),
        );
    }
}
