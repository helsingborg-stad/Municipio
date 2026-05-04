<?php

namespace Municipio\Content\HeadingStructure;

use PHPUnit\Framework\TestCase;

class LegacyDomAdapterTest extends TestCase
{
    public function testRenameHeadingElementPreservesChildrenAndAttributes(): void
    {
        $subject = new LegacyDomAdapter();
        $subject->load('<h3 class="title"><span>Heading</span></h3>');

        $heading = $subject->getHeadingElements()[0];
        $subject->renameHeadingElement($heading, 'h2');

        $this->assertSame('<h2 class="title"><span>Heading</span></h2>' . PHP_EOL, $subject->saveHtml());
    }

    public function testFindAutoPromoteCandidateReturnsLegacyDomElement(): void
    {
        $subject = new LegacyDomAdapter();
        $subject->load('<div data-autopromote="1">Title</div><h3>Subtitle</h3>');

        $candidate = $subject->findAutoPromoteCandidate();

        $this->assertNotNull($candidate);
        $this->assertSame('div', $subject->getTagName($candidate));
    }
}
