<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class HtmlCommentRemoveProcessorTest extends TestCase
{
    #[TestDox('removes HTML comments from the markup when WP_DEBUG is disabled')]
    public function testProcessRemovesComments(): void
    {
        $processor = new HtmlCommentRemoveProcessor();
        $input = '<div>Content<!-- This is a comment --></div>';
        $expectedOutput = '<div>Content</div>';
        $this->assertEquals($expectedOutput, $processor->process($input));
    }

    #[TestDox('does not remove HTML comments from the markup when WP_DEBUG is enabled')]
    public function testProcessKeepsCommentsWhenDebugEnabled(): void
    {
        define('WP_DEBUG', true);
        $processor = new HtmlCommentRemoveProcessor();
        $input = '<div>Content<!-- This is a comment --></div>';
        $expectedOutput = '<div>Content<!-- This is a comment --></div>';
        $this->assertEquals($expectedOutput, $processor->process($input));
    }
}
