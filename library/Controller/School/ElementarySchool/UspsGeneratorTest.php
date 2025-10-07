<?php

namespace Municipio\Controller\School\ElementarySchool;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetTheTerms;
use WpService\Implementations\FakeWpService;

class UspsGeneratorTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $generator = new UspsGenerator(Schema::elementarySchool(), 1, new FakeWpService());
        $this->assertInstanceOf(UspsGenerator::class, $generator);
    }
}
