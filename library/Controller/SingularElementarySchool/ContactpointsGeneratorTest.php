<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularElementarySchool;

use PHPUnit\Framework\TestCase;

class ContactpointsGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool();
        $generator        = new ContactpointsGenerator($elementarySchool);
        $this->assertInstanceOf(ContactpointsGenerator::class, $generator);
    }

    public function testGenerateReturnsEmptyArrayIfContactPointIsNotArray(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->contactPoint(null);
        $generator        = new ContactpointsGenerator($elementarySchool);
        $this->assertSame([], $generator->generate());
    }

    public function testGenerateReturnsEmptyArrayIfContactPointIsEmptyArray(): void
    {
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->contactPoint([]);
        $generator        = new ContactpointsGenerator($elementarySchool);
        $this->assertSame([], $generator->generate());
    }

    public function testGenerateReturnsValidItemsForValidContactPoints(): void
    {
        $contactPoint     = \Municipio\Schema\Schema::contactPoint()
            ->url('https://twitter.com/test')
            ->contactType('socialmedia')
            ->name('Twitter');
        $elementarySchool = \Municipio\Schema\Schema::elementarySchool()->contactPoint([$contactPoint]);
        $generator        = new ContactpointsGenerator($elementarySchool);
        $result           = $generator->generate();
        $this->assertEquals([
            'items' => [
                [
                    'name' => 'Twitter',
                    'url'  => 'https://twitter.com/test',
                    'icon' => 'Twitter',
                ]
            ]
        ], $result);
    }
}
