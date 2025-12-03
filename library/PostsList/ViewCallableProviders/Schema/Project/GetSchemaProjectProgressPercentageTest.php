<?php

namespace Municipio\PostsList\ViewCallableProviders\Schema\Project;

use Municipio\PostObject\NullPostObject;
use PHPUnit\Framework\TestCase;

class GetSchemaProjectProgressPercentageTest extends TestCase
{
    public function testGetSchemaProjectProgressPercentage(): void
    {
        $post = new class extends NullPostObject {
            public function getId(): int
            {
                return 1;
            }

            public function getSchemaProperty(string $property): mixed
            {
                if ($property === 'status') {
                    return ['number' => 75];
                }

                return null;
            }
        };

        $getProgressPercentage = new GetProgressPercentage();
        $callable              = $getProgressPercentage->getCallable();
        $result                = $callable($post);

        $this->assertEquals(75, $result);
    }
}
