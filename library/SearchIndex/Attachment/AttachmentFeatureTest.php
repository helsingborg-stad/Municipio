<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Attachment;

use AcfService\Implementations\FakeAcfService;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class AttachmentFeatureTest extends TestCase
{
    public function testAddsAttachmentPostTypeAndStatus(): void
    {
        $feature = $this->createFeature(new FakeWpService());

        static::assertSame(['post', 'attachment'], $feature->addAttachmentPostType(['post']));
        static::assertSame(['publish', 'inherit'], $feature->addAttachmentPostStatus(['publish']));
    }

    public function testDoesNotAddAttachmentsWhenNoMimeTypesAreSelected(): void
    {
        $feature = new AttachmentFeature(
            new FakeWpService(),
            new AttachmentConfig(new FakeAcfService(['getField' => false])),
        );

        static::assertSame(['post'], $feature->addAttachmentPostType(['post']));
        static::assertSame(['publish'], $feature->addAttachmentPostStatus(['publish']));
    }

    public function testIndexesOnlySelectedAttachmentMimeTypes(): void
    {
        $wpService = new FakeWpService([
            'getPostType' => 'attachment',
            'getPostMimeType' => 'application/pdf',
        ]);
        $feature = $this->createFeature($wpService);

        static::assertTrue($feature->shouldIndexAttachment(true, 42));

        $otherMimeFeature = $this->createFeature(new FakeWpService([
            'getPostType' => 'attachment',
            'getPostMimeType' => 'image/jpeg',
        ]));
        static::assertFalse($otherMimeFeature->shouldIndexAttachment(true, 42));
    }

    public function testLeavesNonAttachmentEligibilityUnchanged(): void
    {
        $feature = $this->createFeature(new FakeWpService(['getPostType' => 'post']));

        static::assertTrue($feature->shouldIndexAttachment(true, 42));
        static::assertFalse($feature->shouldIndexAttachment(false, 42));
    }

    public function testUsesDirectAttachmentUrl(): void
    {
        $feature = $this->createFeature(new FakeWpService([
            'wpGetAttachmentUrl' => 'https://example.test/file.pdf',
        ]));

        $record = $feature->addAttachmentDetails(['post_type' => 'attachment'], 42);

        static::assertSame('https://example.test/file.pdf', $record['permalink']);
    }

    public function testTriggersGenericIndexAndDeleteActions(): void
    {
        $wpService = new FakeWpService(['doAction' => null]);
        $feature = $this->createFeature($wpService);

        $feature->indexAttachment(42);
        $feature->deleteAttachment(42);

        static::assertSame('Municipio/SearchIndex/IndexPostId', $wpService->methodCalls['doAction'][0][0]);
        static::assertSame('Municipio/SearchIndex/DeletePostId', $wpService->methodCalls['doAction'][1][0]);
    }

    private function createFeature(FakeWpService $wpService): AttachmentFeature
    {
        return new AttachmentFeature(
            $wpService,
            new AttachmentConfig(new FakeAcfService(['getField' => ['application/pdf']])),
        );
    }
}