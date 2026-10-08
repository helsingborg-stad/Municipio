<?php

declare(strict_types=1);

namespace Municipio\Integrations\Component;

use ComponentLibrary\Integrations\Image\ImageInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ImageAltTextIndicatorTest extends TestCase
{
    public function testRegistersTheImageDataFilter(): void
    {
        $wpService = new FakeWpService(['addFilter' => true]);

        (new ImageAltTextIndicator($wpService))->addHooks();

        static::assertSame(
            'ComponentLibrary/Component/Image/Data',
            $wpService->methodCalls['addFilter'][0][0],
        );
    }

    public function testAddsIndicatorForEditorWhenAltTextIsMissing(): void
    {
        $wpService = new FakeWpService([
            'isUserLoggedIn' => true,
            'currentUserCan' => true,
            '__' => static fn(string $text): string => $text,
        ]);

        $data = (new ImageAltTextIndicator($wpService))->addIndicator([
            'alt' => '',
            'attributeList' => ['data-component' => 'image'],
        ]);

        static::assertSame('Alt text is missing', $data['attributeList']['data-a11y-error']);
        static::assertSame('image', $data['attributeList']['data-component']);
    }

    public function testDoesNotAddIndicatorForVisitorsOrImagesWithAltText(): void
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
            (new ImageAltTextIndicator($visitor))->addIndicator(['alt' => '']),
        );
        static::assertArrayNotHasKey(
            'data-a11y-error',
            (new ImageAltTextIndicator($editor))->addIndicator(['alt' => 'A town hall']),
        );
    }

    public function testDoesNotAddIndicatorWhenImageContractSuppliesAltText(): void
    {
        $wpService = new FakeWpService([
            'isUserLoggedIn' => true,
            'currentUserCan' => true,
        ]);
        $image = $this->createStub(ImageInterface::class);
        $image->method('getAltText')->willReturn('Two people sit on a sofa.');

        $data = (new ImageAltTextIndicator($wpService))->addIndicator([
            'alt' => '',
            'src' => $image,
        ]);

        static::assertArrayNotHasKey('data-a11y-error', $data);
    }
}
