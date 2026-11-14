<?php

declare(strict_types=1);

namespace Municipio\Chat\Render\Integration;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class PuzzelTest extends TestCase
{
    #[TestDox('implements RenderIntegrationInterface')]
    public function testImplementsInterface(): void
    {
        static::assertInstanceOf(RenderIntegrationInterface::class, new Puzzel());
    }

    #[TestDox('transform() maps all Puzzel settings to attributes with a lowercased name prefix')]
    public function testTransformMapsAllAttributes(): void
    {
        $result = (new Puzzel())->transform([], [
            'name' => 'Support',
            'customer_id' => '123',
            'queue_key' => 'Q_KEY',
            'visual_queue_id' => '456',
            'time_id' => '789',
            'client_id' => 'client',
            'avatar' => 'avatar.png',
        ]);

        static::assertSame([
            'attributeList' => [
                'data-js-chat-support-customer-id' => '123',
                'data-js-chat-support-queue-key' => 'Q_KEY',
                'data-js-chat-support-visual-queue-id' => '456',
                'data-js-chat-support-time-id' => '789',
                'data-js-chat-support-client-id' => 'client',
                'data-js-chat-support-name' => 'Support',
                'data-js-chat-support-avatar' => 'avatar.png',
            ],
        ], $result);
    }

    #[TestDox('transform() defaults missing settings to empty strings')]
    public function testTransformDefaultsMissingKeys(): void
    {
        $result = (new Puzzel())->transform([], ['name' => 'Support']);

        static::assertSame([
            'data-js-chat-support-customer-id' => '',
            'data-js-chat-support-queue-key' => '',
            'data-js-chat-support-visual-queue-id' => '',
            'data-js-chat-support-time-id' => '',
            'data-js-chat-support-client-id' => '',
            'data-js-chat-support-name' => 'Support',
            'data-js-chat-support-avatar' => '',
        ], $result['attributeList']);
    }

    #[TestDox('transform() merges into an existing attributeList and keeps other data untouched')]
    public function testTransformMergesExistingAttributeList(): void
    {
        $data = [
            'id' => 'chat',
            'attributeList' => [
                'data-existing' => 'keep',
                'data-js-chat-support-queue-key' => 'old',
            ],
        ];

        $result = (new Puzzel())->transform($data, ['name' => 'Support', 'queue_key' => 'new']);

        static::assertSame('chat', $result['id']);
        static::assertSame('keep', $result['attributeList']['data-existing']);
        static::assertSame('new', $result['attributeList']['data-js-chat-support-queue-key']);
        static::assertCount(8, $result['attributeList']);
    }
}
