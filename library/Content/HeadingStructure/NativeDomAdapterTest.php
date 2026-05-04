<?php

namespace Municipio\Content\HeadingStructure;

use PHPUnit\Framework\TestCase;

class NativeDomAdapterTest extends TestCase
{
    public function testRenameHeadingElementPreservesChildrenAndAttributesWhenHtmlDocumentIsAvailable(): void
    {
        if (!class_exists(\DOM\HTMLDocument::class)) {
            $this->markTestSkipped('DOM\\HTMLDocument is only available on PHP 8.4+.');
        }

        $subject = new NativeDomAdapter();
        $subject->load('<h3 class="title"><span>Heading</span></h3>');

        $heading = $subject->getHeadingElements()[0];
        $subject->renameHeadingElement($heading, 'h2');

        $this->assertSame('<h2 class="title"><span>Heading</span></h2>', trim($subject->saveHtml()));
    }
}
