<?php

declare(strict_types=1);

namespace Municipio\SchemaData\ApplySchemaDataToSearchIndexRecord\SchemaFromPostId;

use Municipio\Schema\BaseType;

class SchemaFromPostIdWithDatesAsTimestamps implements SchemaFromPostIdInterface
{
    public function __construct(
        private SchemaFromPostIdInterface $inner,
    ) {}

    public function getSchema(int $postId): BaseType
    {
        return $this->convert(clone $this->inner->getSchema($postId));
    }

    private function convert(BaseType $schema): BaseType
    {
        foreach ($schema->getProperties() as $key => $value) {
            if ($value instanceof \DateTime) {
                $schema->setProperty($key, $value->getTimestamp());
            }

            if ($value instanceof BaseType) {
                $schema->setProperty($key, $this->convert($value));
            }

            if (is_array($value)) {
                foreach ($value as $index => $event) {
                    if (!$event instanceof BaseType) {
                        continue;
                    }

                    $value[$index] = $this->convert($event);
                }
                $schema->setProperty($key, $value);
            }
        }

        return $schema;
    }
}
