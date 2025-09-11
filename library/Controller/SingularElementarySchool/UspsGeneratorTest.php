<?php

namespace Municipio\Controller\SingularElementarySchool;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetTheTerms;

class UspsGeneratorTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $elementarySchool = Schema::elementarySchool();
        $wpService        = new class implements GetTheTerms {
            public function getTheTerms(int|\WP_Post $post, string $taxonomy): array|false|\WP_Error
            {
                return [];
            }
        };

        $generator = new UspsGenerator($elementarySchool, 1, $wpService);
        $this->assertInstanceOf(UspsGenerator::class, $generator);
    }
}
