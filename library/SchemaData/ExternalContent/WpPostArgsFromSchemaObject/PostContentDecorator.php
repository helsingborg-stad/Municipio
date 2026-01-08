<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\BaseType;
use Municipio\Schema\TextObject;

class PostContentDecorator implements WpPostArgsFromSchemaObjectInterface
{
    private const POST_CONTENT_KEY = 'post_content';

    public function __construct(
        private WpPostArgsFromSchemaObjectInterface $inner,
    ) {}

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        $postArgs = $this->inner->transform($schemaObject);
        $description = $schemaObject->getProperty('description');

        if (empty($description)) {
            return $postArgs;
        }

        return [
            ...$postArgs,
            self::POST_CONTENT_KEY => $this->formatDescriptionAsPostContent($description),
        ];
    }

    /**
     * Formats the description property into a string suitable for post content.
     *
     * @param array|string|TextObject|null $description
     * @return string
     */
    private function formatDescriptionAsPostContent(array|string|TextObject|null $description): string
    {
        if (empty($description)) {
            return '';
        }

        if (is_string($description)) {
            return $description;
        }

        if ($description instanceof TextObject) {
            $text = $description->getProperty('text') ?? '';
            $title = $description->getProperty('headline');

            if (!empty($title)) {
                return "{$title}\n{$text}";
            }

            return $text;
        }

        if (is_array($description)) {
            return implode(PHP_EOL, array_map(
                fn($item) => $this->formatDescriptionAsPostContent($item),
                $description,
            ));
        }

        return '';
    }
}
