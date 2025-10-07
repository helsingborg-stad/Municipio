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
        $result                = $applyMailHtmlTemplate->apply([]);

        $this->assertEquals('html', $result['message']);
    }

    private function getHtmlTemplate(): HtmlTemplate
    {
        return new class implements HtmlTemplate {
            public function setSubject(string $content): void
            {
            }

            public function setContent(string $content): void
            {
            }

            public function getHtml(): string
            {
                return 'html';
            }
        };
    }

    private function getWpService(): AddFilter
    {
        return new class implements AddFilter {
            public array $filtersAdded = [];

            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->filtersAdded[] = [$hookName, $callback, $priority, $acceptedArgs];
                return true;
            }
        };
    }
}
