<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\Wpautop;

class MapDescriptionTest extends TestCase
{
    #[TestDox('maps description containing TextObject to plain text')]
    public function testMapDescriptionWithTextObject()
    {
        $mapper = new MapDescription($this->getNullWpService());
        $event  = Schema::event()->description([
            Schema::textObject()->text('This is a text object.'),
        ]);

        $this->assertEquals('This is a text object.', $mapper->map($event));
    }

    #[TestDox('maps description containing plain text to plain text')]
    public function testMapDescriptionWithPlainText()
    {
        $mapper = new MapDescription($this->getNullWpService());
        $event  = Schema::event()->description([
            'This is plain text.',
        ]);

        $this->assertEquals('This is plain text.', $mapper->map($event));
    }

    #[TestDox('maps description containing both TextObject and plain text to plain text')]
    public function testMapDescriptionWithBoth()
    {
        $mapper = new MapDescription($this->getNullWpService());
        $event  = Schema::event()->description([
            Schema::textObject()->text('This is a text object. '),
            'This is plain text.',
            Schema::textObject()->text(' More text object.'),
        ]);

        $this->assertEquals('This is a text object. This is plain text. More text object.', $mapper->map($event));
    }

    #[TestDox('maps empty description to empty string')]
    public function testMapEmptyDescription()
    {
        $mapper = new MapDescription($this->getNullWpService());

        $this->assertEquals('', $mapper->map(Schema::event()->description([])));
        $this->assertEquals('', $mapper->map(Schema::event()->description(null)));
    }

    #[TestDox('maps description containing unexpected types to empty string')]
    public function testMapDescriptionWithUnexpectedTypes()
    {
        $mapper = new MapDescription($this->getNullWpService());
        $event  = Schema::event()->description([
            123,
            new \stdClass(),
            true,
        ]);

        $this->assertEquals('', $mapper->map($event));
    }

    #[TestDox('applies wpautop to the final description')]
    public function testMapDescriptionAppliesWpautop()
    {
        $wpService = new class implements Wpautop {
            public function wpautop(string $text, bool $br = true): string
            {
                return '<p>' . $text . '</p>';
            }
        };

        $mapper = new MapDescription($wpService);
        $event  = Schema::event()->description(['This is plain text.']);

        $this->assertEquals('<p>This is plain text.</p>', $mapper->map($event));
    }

    private function getNullWpService(): Wpautop
    {
        return new class implements Wpautop {
            public function wpautop(string $text, bool $br = true): string
            {
                return $text;
            }
        };
    }
}
