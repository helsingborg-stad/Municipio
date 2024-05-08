<?php

namespace Municipio\BrandedEmails;

use Municipio\BrandedEmails\HtmlTemplate\HtmlTemplate;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class ApplyMailHtmlTemplateTest extends TestCase
{
    /**
     * @testdox apply() applies html template
     */
    public function testApplyAppliesHtmlTemplate()
    {
        $applyMailHtmlTemplate = new ApplyMailHtmlTemplate($this->getHtmlTemplate(), $this->getWpService());

        $result = $applyMailHtmlTemplate->apply(['message' => 'message']);

        $this->assertEquals('headermessagefooter', $result['message']);
    }

    private function getHtmlTemplate(): HtmlTemplate
    {
        return new class implements HtmlTemplate {
            public function getHeader(): string
            {
                return 'header';
            }

            public function getFooter(): string
            {
                return 'footer';
            }
        };
    }

    private function getWpService(): AddFilter
    {
        return new class implements AddFilter {
            public array $filtersAdded = [];

            public function addFilter(string $tag, callable $functionToAdd, int $priority = 10, int $acceptedArgs = 1): bool
            {
                $this->filtersAdded[] = [$tag, $functionToAdd, $priority, $acceptedArgs];
                return true;
            }
        };
    }
}
