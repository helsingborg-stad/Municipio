<?php

namespace Municipio\Controller\SingularPreschool;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class UspsGeneratorTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $generator = new UspsGenerator(Schema::preschool(), 1, new FakeWpService());
        $this->assertInstanceOf(UspsGenerator::class, $generator);
    }
}
