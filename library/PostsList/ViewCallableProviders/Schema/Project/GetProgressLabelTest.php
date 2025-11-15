<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Project;

use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\TestCase;

class GetProgressLabelTest extends TestCase
{
    public function testGetProgressLabel(): void
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

        $getProgressLabel = new GetProgressLabel();
        $callable         = $getProgressLabel->getCallable();
        $result           = $callable($post);

        $this->assertEquals('In Progress', $result);
    }
}
