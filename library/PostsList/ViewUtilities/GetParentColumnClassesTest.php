<?php

namespace Municipio\PostsList\ViewUtilities;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetParentColumnClassesTest extends TestCase
{
    #[TestDox('returns an array of strings')]
    public function testGetParentColumnClasses(): void
    {
        $getParentColumnClasses = new GetParentColumnClasses();

        $callable = $getParentColumnClasses->getCallable();
        $result   = $callable();

        $this->assertIsArray($result);

        foreach ($result as $class) {
            $this->assertIsString($class);
        }
    }
}
