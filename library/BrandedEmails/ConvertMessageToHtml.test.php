<?php

namespace Municipio\BrandedEmails;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;
use WpService\Contracts\Autop;

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

    private function getWpService(): AddFilter&Autop
    {
        return new class implements AddFilter, Autop {
            public function addFilter(
                string $tag,
                callable $functionToAdd,
                int $priority = 10,
                int $acceptedArgs = 1
            ): bool {
                return true;
            }

            public function autop(string $text, bool $br = true): string
            {
                return '<p>' . $text . '</p>';
            }
        };
    }
}
