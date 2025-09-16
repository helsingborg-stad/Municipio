<?php

declare(strict_types=1);

namespace Municipio\Controller\SingularPreschool;

use PHPUnit\Framework\TestCase;

class ContactpointsGeneratorTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool();
        $generator = new ContactpointsGenerator($preschool);
        $this->assertInstanceOf(ContactpointsGenerator::class, $generator);
    }

    public function testGenerateReturnsEmptyArrayIfContactPointIsNotArray(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->contactPoint(null);
        $generator = new ContactpointsGenerator($preschool);
        $this->assertSame([], $generator->generate());
    }

    public function testGenerateReturnsEmptyArrayIfContactPointIsEmptyArray(): void
    {
        $preschool = \Municipio\Schema\Schema::preschool()->contactPoint([]);
        $generator = new ContactpointsGenerator($preschool);
        $this->assertSame([], $generator->generate());
    }

    public function testGenerateReturnsValidItemsForValidContactPoints(): void
    {
        $contactPoint = \Municipio\Schema\Schema::contactPoint()
            ->url('https://twitter.com/test')
            ->contactType('socialmedia')
            ->name('Twitter');
        $preschool    = \Municipio\Schema\Schema::preschool()->contactPoint([$contactPoint]);
        $generator    = new ContactpointsGenerator($preschool);
        $result       = $generator->generate();
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
