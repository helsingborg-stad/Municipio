<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\Schema;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PostContentDecoratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $decorator = new PostContentDecorator($this->createInnerMock());
        static::assertInstanceOf(PostContentDecorator::class, $decorator);
    }

    public function testTransformAddsPostContent(): void
    {
        $decorator = new PostContentDecorator($this->createInnerMock());
        $schemaObject = Schema::thing()->description('Sample Description');
        $result = $decorator->transform($schemaObject);

        static::assertEquals(['post_content' => 'Sample Description'], $result);
    }

    public function testTransformDescriptionArrayToEmptyPostContent(): void
    {
        $decorator = new PostContentDecorator($this->createInnerMock());
        $schemaObject = Schema::thing()->description(['Sample Description', 'Another description']);
        $result = $decorator->transform($schemaObject);

        static::assertEquals(['post_content' => "Sample Description\nAnother description"], $result);
    }

    public function testTransformHandlesTextObjectDescription(): void
    {
        $decorator = new PostContentDecorator($this->createInnerMock());
        $schemaObject = Schema::thing()->description(Schema::textObject()->text('Text from TextObject'));
        $result = $decorator->transform($schemaObject);

        static::assertEquals(['post_content' => 'Text from TextObject'], $result);
    }

    public function testTransformPrependsHeadlineOfTextObjectAsTitle(): void
    {
        $decorator = new PostContentDecorator($this->createInnerMock());
        $schemaObject = Schema::thing()->description(
            Schema::textObject()->headline('Headline')->text('Text from TextObject'),
        );
        $result = $decorator->transform($schemaObject);

        static::assertEquals(['post_content' => "Headline\nText from TextObject"], $result);
    }

    private function createInnerMock(): WpPostArgsFromSchemaObjectInterface|MockObject
    {
        return $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
    }
}
