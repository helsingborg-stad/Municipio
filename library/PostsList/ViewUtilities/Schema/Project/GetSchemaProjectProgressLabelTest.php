<?php

namespace Municipio\PostsList\ViewUtilities\Schema\Project;

use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\TestCase;

class GetSchemaProjectProgressLabelTest extends TestCase
{
    public function testGetSchemaProjectProgressLabel(): void
    {
        $post = new class extends NullPostObject {
            public function getId(): int
            {
                return 1;
            }

            public function getSchemaProperty(string $property): mixed
            {
                if ($property === 'status') {
                    return ['name' => 'In Progress'];
                }

                return null;
            }
        };

        $getSchemaProjectProgressLabel = new GetSchemaProjectProgressLabel();
        $callable                      = $getSchemaProjectProgressLabel->getCallable();
        $result                        = $callable($post);

        $this->assertEquals('In Progress', $result);
    }
}
