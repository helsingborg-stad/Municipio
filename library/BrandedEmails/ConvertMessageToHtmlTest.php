<?php

namespace Municipio\BrandedEmails;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;
use WpService\Contracts\Wpautop;

class ConvertMessageToHtmlTest extends TestCase
{
    /**
     * @testdox applies autop to message
     */
    public function testConvertMessageToHtml(): void
    {
        $convert = new ConvertMessageToHtml($this->getWpService());
        $result  = $convert->convertMessageToHtml(['message' => 'message']);

        $this->assertEquals('<p>message</p>', $result['message']);
    }

    private function getWpService(): AddFilter&Wpautop
    {
        return new class implements AddFilter, Wpautop {
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function wpautop(string $text, bool $br = true): string
            {
                return '<p>' . $text . '</p>';
            }
        };
    }
}
